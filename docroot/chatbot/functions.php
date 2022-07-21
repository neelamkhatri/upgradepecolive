<?php

/**
 * 
 */
class message
{
	
	function __construct($agent)
	{
		$this->agent = $agent;		
	}

	function simpleText($message){
	    $this->agent->reply($message);	    
	}

	function richText($message){
		$text = \Dialogflow\RichMessage\Text::create()
		->text($message)
		->ssml('<speak>This is <say-as interpret-as="characters">ssml</say-as></speak>');
		$this->agent->reply($text);
	}

	function image($url){
		$image = \Dialogflow\RichMessage\Image::create($url);
		$this->agent->reply($image);
	}

	function card($title,$message,$url,$buttonTitle,$buttonLink){
		$card = \Dialogflow\RichMessage\Card::create()
		->title($title)
		->text($message)
		->image($url)
		->button($buttonTitle, $buttonLink);
		$this->agent->reply($card);
	}

	function suggestion($suggestions){
		$suggestion = \Dialogflow\RichMessage\Suggestion::create($suggestions);
		$this->agent->reply($suggestion);
	}

	function customSuggestions($suggestions){
		$suggestion_array = [];
		foreach($suggestions as $suggestion)
		{
			array_push($suggestion_array,[
				'text' => $suggestion,
			]);
		}

		$this->agent->reply(\Dialogflow\RichMessage\Payload::create(
			[
				'richContent' => [
					[
						[
							'type' => 'chips',
							'options' => $suggestion_array,
						]
					]
				]
			]
		));
	}

	function customImage($image){
		$this->agent->reply(\Dialogflow\RichMessage\Payload::create(
			[
				'richContent' => [
					[
						[
							'type' => 'image',
							'rawUrl' => $image,
							'accessibilityText'=> 'Example logo'
						]
					]
				]
			]
		));
	}

	function customCard($title,$message,$url,$buttonTitle,$buttonLink,$imageUrl=null){
		$this->agent->reply(\Dialogflow\RichMessage\Payload::create(
			[
				'richContent' => [
					[
						[
						  "type"=> "info",
						  "actionLink"=> $url,
						  "image"=> [
							"src"=> [
							  "rawUrl"=> $imageUrl
							]
						  ],
						  "title"=> $title,
						  "subtitle"=> $message
						]
					]
				]
			]
		));
	}

	function customButton($text,$url){
		$this->agent->reply(\Dialogflow\RichMessage\Payload::create(
				[
					'richContent' => [
						[
							[
								'type' => 'button',
								'icon' => [
									'type'=> 'chevron_right',
          							'color'=> '#FF9800'
								],
								'text' => $text,
								'link'=> $url,
								// 'event' => [
								// 	'name' => '',
								// 	'languageCode' => '',
								// 	'parameters' => [],
								// ]
							]
						]
					]
				]
			));
	}

	public function build_image($url, $accessibilityText, $height = null, $width = null) {
		$image = array(
			'url' => $url,
			'accessibilityText' => $accessibilityText,
			'height' => $height,
			'width' => $width,
		);
		return $image;
 }  

}