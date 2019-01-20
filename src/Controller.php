<?php
namespace Framework;

class Controller
{
   private $twig;
   public function __construct()
   {
       session_start();
       $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../app/views');
       $twigEnvParams = $this->prepareTwigEnvironmentParams();
       $twig = new \Twig_Environment($loader, $twigEnvParams);
       $twig->addGlobal('session', $_SESSION);
       $this->twig = $twig;
   }
   public function view(string $viewFile, array $params = [])
   {
       echo $this->twig->render($viewFile, $params); 
   }
   private function prepareTwigEnvironmentParams()
   {
       $envParams['cache'] =  __DIR__ . '/../storage/cache/views';
       if(\App\Config::ENV === 'dev') {
           $envParams['cache'] = false;
           $envParams['debug'] = true;
       }
       return $envParams;
   }
}