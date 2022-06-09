<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\GeneratorStatsRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pagerfanta\View\TwitterBootstrap5View;
use Pagerfanta\View\OptionableView;

class SearchController extends AbstractController
{
    #[Route('/', name: 'app_search')]
    public function search(
        Request $request,
        GeneratorStatsRepository $generatorStatsRepository
    ): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        $queryBuilder = Null;

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['from'] = $data['from']->format('Y-m-d');
            $data['to'] = $data['to']->format('Y-m-d');

            return $this->redirectToRoute('app_get_statistics', ["id" => $data['generator']->getId(), "from" => $data['from'], "to" => $data['to']]);
        }

        return $this->render('search.html.twig', [
            'searchForm' => $form->createView()
        ]);
    }

    #[Route('/statistics/{id}/{from}/{to}', name: 'app_get_statistics')]
    public function get_statistics(
        int $id,
        string $from,
        string $to,
        Request $request,
        GeneratorStatsRepository $generatorStatsRepository
    )
    {
        $queryBuilder = $generatorStatsRepository->getGeneratorStatsInDatePeriodQueryBuilder(
            $id,
            $from,
            $to
        );
//        print("<pre>".print_r($queryBuilder->getQuery()->getResult(),true)."</pre>");
//        die();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );
        $pagerfanta->setMaxPerPage(96);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('get_statistics.html.twig', [
            'pager' => $pagerfanta
        ]);
    }
}
