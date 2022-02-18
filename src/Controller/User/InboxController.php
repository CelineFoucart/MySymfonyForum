<?php

namespace App\Controller\User;

use App\Entity\PrivateMessage;
use App\Entity\Report;
use App\Entity\User;
use App\Form\PrivateMessageType;
use App\Form\ReportType;
use App\Repository\PrivateMessageRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InboxController extends AbstractController
{
    private PrivateMessageRepository $repo;
    private UserRepository $userRepository;

    public function __construct(PrivateMessageRepository $repo, UserRepository $userRepository)
    {
        $this->repo = $repo;
        $this->userRepository = $userRepository;
    }

    #[Route('/inbox', name: 'inbox')]
    public function index(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $privateMessages = $user->getReceivedPrivateMessages();

        return $this->render('user/inbox/index.html.twig', [
            'privateMessages' => $privateMessages,
        ]);
    }

    #[Route('/inbox/sent', name: 'inbox_sent')]
    public function sent(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $privateMessages = $user->getPrivateMessages();

        return $this->render('user/inbox/index.html.twig', [
            'privateMessages' => $privateMessages,
            'label' => 'Messages envoyés',
        ]);
    }

    #[Route('/inbox/new', name: 'inbox_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reply = $this->repo->findOneById($request->query->getInt('reply', 0));
        $replyTo = $request->query->get('replyTo', '');
        $pm = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $pm);
        if (null !== $reply) {
            $form->get('addressee')->setData($reply->getAuthor()->getUsername());
            $form->get('title')->setData("Re: {$reply->getTitle()}");
        } elseif (null != $replyTo) {
            $form->get('addressee')->setData($replyTo);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('addressee')->getData();
            $addressee = $this->userRepository->findByPseudo($pseudo);
            if (null === $addressee) {
                $this->addFlash(
                   'error',
                   "L'utilisateur {$pseudo} n'existe pas."
                );
            } else {
                $pm->setCreated(new DateTime())->setAuthor($this->getUser())->setAddressee($addressee);
                $entityManager->persist($pm);
                $entityManager->flush();
                $this->addFlash(
                'success',
                'Le message privé a bien été envoyé.'
                );

                return $this->redirectToRoute('inbox_show', ['id' => $pm->getId()]);
            }
        }

        return $this->render('user/inbox/new.html.twig', [
            'form' => $form->createView(),
            'reply' => $reply,
        ]);
    }

    #[Route('/inbox/{id}', name: 'inbox_show')]
    public function show(int $id): Response
    {
        $privateMessage = $this->findPrivateMessage($id);

        return $this->render('user/inbox/show.html.twig', [
            'privateMessage' => $privateMessage,
        ]);
    }

    #[Route('/inbox/{id}/report', name: 'inbox_report')]
    public function report(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $privateMessage = $this->findPrivateMessage($id);
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setPrivateMessage($privateMessage)->setAuthor($this->getUser())->setType('pm');
            $em->persist($report);
            $em->flush();
            $this->addFlash('success', 'Votre rapport a bien été envoyé.');

            return $this->redirectToRoute('inbox_show', ['id' => $privateMessage->getId()]);
        }

        return $this->render('user/inbox/inbox_report.html.twig', [
            'privateMessage' => $privateMessage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/inbox/{id}/delete', name: 'inbox_delete')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $privateMessage = $this->findPrivateMessage($id);
        if ($request->isMethod('POST')
            && $this->isCsrfTokenValid('delete'.$privateMessage->getId(), $request->request->get('_token'))) {
            foreach ($privateMessage->getReports() as $report) {
                $em->remove($report);
            }
            $em->remove($privateMessage);
            $this->addFlash('success', 'Le message a bien été supprimé.');
            $em->flush();

            return $this->redirectToRoute('inbox');
        }

        return $this->render('user/inbox/delete.html.twig', [
            'privateMessage' => $privateMessage,
        ]);
    }

    private function findPrivateMessage(int $id): PrivateMessage
    {
        $privateMessage = $this->repo->findOneById($id);
        if (null === $privateMessage) {
            throw $this->createNotFoundException('Ce message n\'existe pas');
        }
        $users = [$privateMessage->getAuthor()->getId(), $privateMessage->getAddressee()->getId()];
        $user = $this->getUser();
        assert($user instanceof User);
        if (!in_array($user->getId(), $users)) {
            throw $this->createNotFoundException('Ce message n\'existe pas');
        }

        return $privateMessage;
    }
}
