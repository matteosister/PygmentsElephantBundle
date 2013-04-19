<?php
/**
 * User: matteo
 * Date: 04/12/12
 * Time: 0.46
 * 
 * Just for fun...
 */

namespace Cypress\PygmentsElephantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MainController
 *
 * @package Cypress\PygmentsElephantBundle\Controller
 */
class MainController extends Controller
{
    /**
     * css for pygments
     *
     * @return Response
     */
    public function cssAction()
    {
        $pygmentize = $this->get('cypress.pygments_elephant.pygmentize');
        $response = new Response();
        $response->headers->set('content-type', 'text/css');
        $response->setContent($pygmentize->generateCss());

        return $response;
    }
}
