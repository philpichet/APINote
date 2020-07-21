<?php

namespace App\Controller;

use App\Repository\GradeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param GradeRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function average(GradeRepository $repository)
    {
        $average = $repository->getAverageOfAll();
        return $this->json(['average' => round($average, 2)],200);
    }
}
