<?php

namespace App\Controller;

use App\Repository\GradesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GradesController
 * @package App\Controller
 *
 * @Route("/api/grades", name="api_students_",condition="request.headers.get('Accept') === 'application/json' and request.headers.get('Content-Type') === 'application/json'")
 */
class GradesController extends AbstractController
{
    /**
     * Return the average of all grades in DB
     * @Route("/average", name="average", methods={"GET"})
     * @param GradesRepository $repository
     * @return JsonResponse
     */
    public function average(GradesRepository $repository)
    {
        $average = $repository->getAverageOfAll();
        return $this->json(['average' => round($average, 2)],200);
    }
}
