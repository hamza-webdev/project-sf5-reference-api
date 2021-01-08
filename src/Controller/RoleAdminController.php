<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleAdminController extends AbstractController
{
    /**
     * @Route("/admin", name="role_admin")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('role_admin/index.html.twig', [
            'controller_name' => 'RoleAdminController',
        ]);
    }

    /**
     * @Route("/denied", name="app_denied")
     */
     public function denied()
     {
                  
         return $this->render('role_admin/denie.html.twig', [
             'controller_name' => 'RoleAdminController',
         ]);
     }
}
