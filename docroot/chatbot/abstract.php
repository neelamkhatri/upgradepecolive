<?php

include './vendor/autoload.php';
use Dialogflow\WebhookClient;

$agent = new WebhookClient(json_decode(file_get_contents('php://input'),true));

$intent = $agent->getIntent();

$action = $agent->getAction();

$query = $agent->getQuery();

$parameters = $agent->getParameters();

$session = $agent->getSession();

$language = $agent->getLocale();

$originalRequest = $agent->getRequestSource();

$originalRequest = $agent->getOriginalRequest();

$agentVersion = $agent->getAgentVersion();