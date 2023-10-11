<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\SellerRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }

    #[Route('/booking/{id}/{week}', name: 'app_seller_booking', defaults: ["week" => null], methods: ['GET', 'POST'])]
    public function booking(SellerRepository $sellerRepository, $id, ?int $week, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Récupération de la semaine actuelle ou de la semaine saisie
        $currentWeek = $week ?? date('W');

        $seller = $sellerRepository->findOneBy(['id' => $id]);

        //Récupérer tous les rendez-vous d'un vendeur
        $bookings = $seller->getBookings();

        $bookedSlots = [];
        foreach ($bookings as $booking) {
            if ($booking->getWeek() == $currentWeek) {
                $bookedSlots[$booking->getDay()][$booking->getTime()] = true;
            }
        }

        //Récupérer l'utilisateur qui est connecté
        $user = $this->getUser();

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        //Vérification si le formulaire est soumis et correct
        if ($form->isSubmitted() && $form->isValid()) {
            $day = $form->get('day')->getData();
            $time = $form->get('time')->getData();
            $booking->setDay($day)
                ->setTime($time)
                ->setClient($user)
                ->setSeller($seller)
                ->setWeek($currentWeek);
            //Préparer la "requête" a envoyer
            $entityManager->persist($booking);
            //Pour sauvegarder en base de données
            $entityManager->flush();
            return $this->redirectToRoute('app_seller_booking', ['id' => $seller->getId(), 'week' => $currentWeek]);
        }

        //Récupérer la durée, si elle n'est pas rentrée l'initialiser à 30 minutes
        $duration = $seller->getDuration() ?? 30;

        //Mettre un horaire de début et de fin de journée
        $startTime = new DateTimeImmutable('9:00');
        $endTime = new DateTimeImmutable('18:00');

        $timeSlots = [];
        while ($startTime < $endTime) {
            $timeSlots[] = $startTime->format('h;i');
            $startTime = $startTime->modify("+$duration minutes");
        }

        $weekSlots = [
            'times' => $timeSlots,
            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
        ];

        $dt = new DateTimeImmutable();
        $dt = $dt->setISODate(date('Y'), $currentWeek);

        setlocale(LC_TIME, 'fr_FR', 'fra');

        $weekDates = [];
        for ($i = 0; $i < 5; $i++) {
            $date = new DateTimeImmutable();
            $date = $date->setISODate((int)$date->format('o'), $currentWeek, $i + 1);
            $weekDates[] = strftime("%A %d/%m/%Y", $date->getTimestamp());
        }

        

        return $this->render('home/booking.html.twig', [
            'seller' => $seller,
            'bookings' => $bookings,
            'weekSlots' => $weekSlots,
            'bookedSlots' => $bookedSlots,
            'booking' => $booking,
            'form' => $form->createView(),
            'currentWeek' => $currentWeek,
            'weekDates' => $weekDates
        ]);
    }
}
