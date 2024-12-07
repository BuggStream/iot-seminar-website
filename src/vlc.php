<?php
	header('Content-type: text/plain');
	
	echo( "test 1 \r\n" );

	$initMessage1 = "Welcome to the ";
	$initMessage2 = " course, hope you'll enjoy the lecture! \r\n ";

	$string1 = 'John';
  	$string2 = 'Doe';
  	$name_str = $string1 . ' ' . $string2;
  	echo $name_str;
	
	class courseMessage 
	{
  		private $course;
  		private $message;

  		function __construct( $course, $message) 
		{
    		$this->course = $course; 
   			$this->message = $message; 
			
			echo "message initialized: " . $this->message;
  		}
		
  		function get_course() 
		{
    		return $this->course;
  		}
		
  		function get_message() 
		{
    		return $this->message;
  		}
		
  		function set_message( $newMessage ) 
		{
    		$this->message = $newMessage;
		}
}
	
	echo( "test 2 \r\n" );
	
	$initMessage = $initMessage1." ".$vlc->get_course(); //. $initMessage2;
	
	$vlc = new courseMessage( "VLC", $initMessage );
	
	//echo $vlc->get_message();
	//echo $vlc->get_course();
	//echo( " course, hope you'll enjoy the lecture! \r\n" );
	
	echo( "test 3 \r\n" );
?>