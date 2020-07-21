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
 * @Route("/api/grades", name="api_students_")
 */
class GradesController extends AbstractController
{
    /**
     * @Route("", name="average", methods={"GET"})
     * @param GradesRepository $repository
     * @return JsonResponse
     */
    public function average(GradesRepository $repository)
    {
        $average = $repository->getAverageOfAll();
        return $this->json(['average' => round($average, 2)],200);
    }
}
