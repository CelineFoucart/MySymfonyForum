<?php

namespace App\EventSubscriber;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $passwordEncoder;
    private $roleRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordEncoder,
        RoleRepository $roleRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleRepository = $roleRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser'],
            BeforeEntityUpdatedEvent::class => ['updateUser'],
            BeforeEntityDeletedEvent::class => ['canDelete'],
        ];
    }

    public function canDelete(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if($entity instanceof User) {
            if($entity->hasRole('ROLE_ADMIN')) {
                throw new AccessDeniedException("Vous ne pouvez pas supprimer un administrateur."); 
            }
        } elseif ($entity instanceof Role) {
            $forbiddenDeletion = ['ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'];
            if(in_array($entity->getTitle(), $forbiddenDeletion)) {
                throw new AccessDeniedException("Vous ne pouvez pas supprimer ce rôle."); 
            }
        } else {
            return;
        }
    }

    public function updateUser(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }
        $this->setPassword($entity);
    }

    public function addUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if ($entity instanceof Role) {
            return;
        }
        $entity->setCreated(new DateTime());
        $entity->setRoles(['ROLE_USER']);
        $entity->setDefaultRole($this->roleRepository->findDefaultRole());

        if (!($entity instanceof User)) {
            return;
        }
        $this->setPassword($entity);
    }

    /**
     * @param User $entity
     */
    public function setPassword(User $entity): void
    {
        $pass = $entity->getPassword();

        $entity->setPassword(
            $this->passwordEncoder->hashPassword(
                $entity,
                $pass
            )
        );
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}