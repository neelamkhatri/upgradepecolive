<?php

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

$autoloader = require_once 'autoload.php';

$kernel = new DrupalKernel('prod', $autoloader);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
// $response->send();

$kernel->terminate($request, $response);

require '../docroot/chatbot/vendor/autoload.php';

// $result = space_available('texas');
// echo "<pre>Data:"; echo json_encode($result); echo "</pre>";
// exit;

include 'chatbot/abstract.php';
include 'chatbot/functions.php';

header('Content-type: application/json');


// KC it's for you, please do not delete anything.
$message  = new message($agent);

switch ($intent) {
	case 'restaurant_searching':

		$result = chatbot_dialogflow();
		$result = json_encode($result);
		$message->simpleText($result);

		$message->simpleText("Okay, I am sending you the SENDDDING Thank youu My love god.");
		// $agent->reply(\Dialogflow\RichMessage\Payload::create(
		// 	$message->custom_suggestions(['Saket','Prathamesh','mysql'])
		// ));
		$message->customImage('https://p.bigstockphoto.com/GeFvQkBbSLaMdpKXF1Zv_bigstock-Aerial-View-Of-Blue-Lakes-And--227291596.jpg');
		$message->customButton('Look at the fabulous site here', 'https://google.com');
		$message->customSuggestions(['Saket', 'Prathamesh', 'mysql']);
		// $message->image("https://images.unsplash.com/photo-1476085259528-8b1839cff31c");
		// $message->custom_payload();
		// $message->build_image();
		// $message->card('This is title','this is text body','https://images.unsplash.com/photo-1476085259528-8b1839cff31c','This is a button', 'https://docs.dialogflow.com/');
		// echo json_encode($agent->render());
		break;

	case 'getDataFromMySQL':
		$message->simpleText("This is Mysql: ");
		break;
	case 'properties_in_state':
		if(!empty($parameters['location']['admin-area'])){
			$result = count_properties($parameters['location']['admin-area']);
			$message->simpleText("Phillips Edison owns or manages neighborhood shopping centers in ". $parameters['location']['admin-area'] .". There are currently ". $result ." properties in ". $parameters['location']['admin-area'] .".");
			$message->customButton('Click here for a list of properties in '. $parameters['location']['admin-area'], 'https://www.phillipsedison.com/properties/'.$parameters['location']['admin-area']); 
			$message->customButton('Click here for spaces available for lease in '. $parameters['location']['admin-area'], "https://www.phillipsedison.com/properties/".$parameters['location']['admin-area']."/all/all?contact=All&population=All&gla=All&use_type=All&available=1&income=All&sqft%5Bmin%5D=0&sqft%5Bmax%5D=99999&title=&overview=&tenant=".$parameters['location']['admin-area']);
		}
		else{
			$message->simpleText('Sorry no property found');
		}
		break;

	case 'contact_acquisition':
		$message->customCard("NATIONAL ACQUISITIONS","David Wik \n Senior Vice President of National Acquisitions and Dispositions (513) 746-2557 dwik@phillipsedison.com","https://www.phillipsedison.com/acquisitions","ac","https://www/phillipsedison.com/about");
		$message->customButton('Click here to learn more', 'https://www.phillipsedison.com/acquisitions');
		break;
	
	case 'space_lease':
		if(!empty($parameters['location']['admin-area'])){
			$results = space_properties($parameters['location']['admin-area']);
			$message->simpleText($results);
			$message->customButton('Click here for a list of properties in '. $parameters['location']['admin-area'], 'https://www.phillipsedison.com/properties/'.$parameters['location']['admin-area']); 
		}
		else{
			$message->simpleText('Sorry no property found');
		}
		break;
	
	case 'acquisition_criteria':
		$message->simpleText("Phillips Edison & Company is always in the market for new acquisition opportunities. Some of our strengths as a buyer include: \n 1. All cash, no financing contingencies 
		2. Single properties or multi-property portfolios 
		3. Will purchase assets or loans
		4. Exceptional record for closing in 30-45 days");
		$message->customButton('Clikc here to know more', 'https://www.phillipsedison.com/acquisitions'); 
		break;

	case 'current_jobs':
		$message->customButton('Click here to learn more about career opportunities at Phillips Edison', "https://recruiting.ultipro.com/PHI1009PHED/JobBoard/d8224c32-ade8-4917-8a97-3ea2d4094e2d?q=&o=postedDateDesc&w=&wc=&we=&wpst=");
		break;	

	case 'neighbors_information':
		$message->customButton('Click here for information on tenants', "https://www.phillipsedison.com/tenants");
		break;

	case 'governance_documents':
		$message->customButton('Click here to view Governance document', "https://www.phillipsedison.com/investors/governance");
		break;

	case 'change_address_investor':
		$message->customButton('Click here to view Investor form', "https://www.phillipsedison.com/investors/investor-forms");
		$message->simpleText("For more information, contact Phillips Edison's Investor Relations customer service team");	
		break;

	case 'available_lease_property':
		if(!empty($parameters['location']['admin-area'])){
			$space_lease = space_available($parameters['location']['admin-area']);
			$message->simpleText("There are " .count($space_lease). " property available for lease in ".$parameters['location']['admin-area']);
			foreach($space_lease as $key=>$properties){
				$message->customCard($properties['name'],"Leasing professional \n ". $properties['agents'] . "\n" .$properties['agent_email'],"https://www.phillipsedison.com/","ac","https://www.phillipsedison.com/property/".$parameters['location']['admin-area']."/".$properties['city']."/".$properties['slug']);
				// $message->simpleText("Property Name: ".$properties['name']. "\n Leasing professional: ".$properties['agents']. "\n Email: ".$properties['agent_email']);
				$message->customButton('Click here to know more about property', "https://www.phillipsedison.com/property/".strtolower($parameters['location']['admin-area'])."/".strtolower($properties['city'])."/".$properties['slug']);
			}
		}
		else{
			$message->simpleText('Sorry no property found');
		}
		break;
		
	case 'Email me the leasing report.':
		$message->simpleText("Okay, I am sending you the report.");
		break;

	case 'FlashBriefing':
		$message->simpleText("Portfolio flash briefing. You have 307 properties. 260 retail and 47 residential. Occupancy is 97  percent, and average rent per square feet is 24.5 dollars. Two anchor tenant leases are expiring soon in your properties in Illinois.");
		break;

	case 'IAm':
		$message->simpleText("Hi " . $parameters['person']['name'] . ", how may i help you?");
		break;

	case 'PetStore':
		$message->simpleText('You have one pet store "Pet Smart" on butter field road.');
		break;

	case 'PropertySummary':
		$message->simpleText("Summary for Rockford Crossings. It is a 118650 square feet retail property located at the NEC with 32800 Annual Average Daily traffic, in the heart of Rockford's retail corridor. Shadow-anchored by Target, this power center has TJ Maxx, DSW, GameStop, Hobby Lobby and Aldi as the top tenants.");
		break;

	case 'ResidentialMaintTickets':
		$message->simpleText("Residential Maintenance Summary for Westmore Apartments. every month you get Thirty-eight new maintenance tickets. As of today, there are twenty open and ten in progress. There is a twelve percent rise in maintenance tickets in last three months, you spent twenty-five percent more money on plumbing repairs.");
		break;

	case 'Stop':
		$message->simpleText("Okay, Bye!");
		break;

	case 'WhoAreYou':
		$message->simpleText("I am Jarvis, may I know your name?");
		break;

	default:
		// $message->simpleText("No response from Prathamesh.");
		$message->customButton('For more queries you can contact on','http://www.phillipsedison.com/contact-us');
		break;
}

echo json_encode($agent->render());
