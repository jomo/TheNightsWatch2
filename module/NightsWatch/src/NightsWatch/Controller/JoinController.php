<?php

namespace NightsWatch\Controller;

use Navarr\MinecraftAPI\Exception\BadLoginException;
use Navarr\MinecraftAPI\Exception\BasicException;
use Navarr\MinecraftAPI\Exception\MigrationException;
use Navarr\MinecraftAPI\MinecraftAPI;
use NightsWatch\Authentication\ForceAdapter;
use NightsWatch\Entity\User;
use NightsWatch\Form\ResetForm;
use NightsWatch\Mvc\Controller\ActionController;
use Zend\Authentication\Storage\Session;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;

class JoinController extends ActionController
{
    /** @var Session */
    protected $session;

    public function __construct()
    {
        $this->session = new SessionContainer('NightsWatch\register');
    }

    public function indexAction()
    {
        return $this->redirect()->toRoute('shotbowlogin');
    }

    public function verifyAction()
    {
        return $this->redirect()->toRoute('shotbowlogin');
    }

    public function recruitAction()
    {
        if ($this->disallowGuest() && $this->disallowRankGreaterThan(User::RANK_RECRUIT)) {
            return false;
        }
        $this->updateLayoutWithIdentity();
        $user = $this->getIdentityEntity();

        $errors = [];
        $hasJoined = $user->rank >= User::RANK_RECRUIT;

        if ($this->getRequest()->isPost()) {
            $user = $this->getIdentityEntity();
            if (!$user->deniedJoin && $user->rank < User::RANK_RECRUIT) {
                $user->rank = User::RANK_RECRUIT;
                if ($user->recruitmentDate == null) {
                    $user->recruitmentDate = new \DateTime();
                }
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
                $hasJoined = true;
            } else {
                $errors[] = 'You do not have the ability to become a recruit.';
            }
        }

        return new ViewModel(['errors' => $errors, 'hasJoined' => $hasJoined]);
    }

    public function resetAction()
    {
        $this->updateLayoutWithIdentity();

        $form = new ResetForm();
        $errors = [];
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                try {
                    $minecraft = new MinecraftAPI(
                        $form->get('username')->getValue(),
                        $form->get('mojangPassword')->getValue()
                    );

                    $user = $this->getEntityManager()
                        ->getRepository('NightsWatch\Entity\User')
                        ->findOneBy(['username' => $minecraft->username]);

                    if (!$user) {
                        $errors[] = 'No Such User';
                    } else {
                        $bcrypt = new Bcrypt();
                        $user->password = $bcrypt->create($form->get('password')->getValue());
                        $this->getEntityManager()->persist($user);
                        $this->getEntityManager()->flush();
                        $this->getAuthenticationService()->authenticate(new ForceAdapter($user->id));
                        $this->updateLayoutWithIdentity();

                        return new ViewModel(['done' => true]);
                    }
                } catch (\RuntimeException $e) {
                    $errors[] = 'Problem querying the API';
                } catch (BadLoginException $e) {
                    $errors[] = 'Invalid username or Password';
                } catch (MigrationException $e) {
                    $errors[] = 'Your Minecraft account has been migrated to a Mojang account.  '
                        .'Please enter your Mojang email and try again';
                } catch (BasicException $e) {
                    $errors[] = 'This is not a premium Minecraft Account';
                }
            }
        }

        return new ViewModel(
            [
                'done'   => false,
                'errors' => $errors,
                'form'   => $form,
            ]
        );
    }
}
