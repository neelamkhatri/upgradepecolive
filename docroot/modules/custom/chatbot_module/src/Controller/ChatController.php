<?php

namespace Drupal\chatbot_module\Controller;

// require drupal_get_path('module', 'chatbot_module').'/vendor/autoload.php';

use Drupal\Core\Controller\ControllerBase;

use Dialogflow\WebhookClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\chatbot_module\message;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routines for contentimport routes.
 */
class ChatController extends ControllerBase{

    public function index() {
		
    }

}
