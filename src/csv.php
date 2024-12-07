<?php 
//header('Content-Type: text/csv; charset=utf-8');

class csvFile
{
 	/*Variables*/
	private $file;
	private $handle;

	/*Methods*/

	public function __construct( $filename, $mode, $header )
	{

        if(!is_string($filename))
           throw new InvalidArgumentException('Parameter $filename must be a string. Provided type: ' . gettype($filename) );

        //if(is_file($filename) || !is_readable($filename))
        //    throw new RuntimeException('The provided file could not be opened for reading. File: ' . $filename);
		
		//echo "...creating file... <br>"; //debugging line
		
		// create file 
        $this->file   = $filename;

        $this->handle = fopen($this->file, $mode);
		//var_dump($header);
		//echo"OPENING WITH ".$mode." fn ".$filename." hd ".$header;
		//return;
		
		//echo "...writing header... <br>"; //debugging line
		
		if( $this->handle === false )
			die( 'Error opening the file ' . $this->file );
		
		// write header to file
		if($mode=='x' || $mode=='w' )fputcsv( $this->handle, $header, ';' );
		
		//echo "writing: " . $field . "<br>"; //debugging line
		
		//echo "...closing file... <br>"; //debugging line
		
		// close the file
		fclose( $this->handle );
		
		//echo "...preparing csv successfully... <br>"; //debugging line

	    // to do: check if file already exists, then operation invalid
	}

	public function write( $dataLine )
	{
		// to do: some validation of the input
		
		//echo "...starting writing data... <br>"; //debugging line
		
		// mode is append
		$mode = 'a';
		
		//open $handle in append mode
		
		//echo "...opening file in append mode... <br>"; //debugging line
		
		$this->handle = fopen($this->file, $mode);
		
		if( $this->handle === false )
			die( 'Error opening the file ' . $file );
		
		//echo "...writing data... <br>"; //debugging line
		
		//$dataLine = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ); //test data
		
		//echo "writing: " . $dataLine . "<br>"; //debugging line
		
		//write the data line to the CSV
		fputcsv( $this->handle, $dataLine, ';' );
		
		//close the file
		//echo "...closing file... <br>"; //debugging line
		
		fclose ( $this->handle );
	}

	public function readFile( )
	{
		//echo "...starting reading the file... <br>"; //debugging line
		
		// mode is append
		$mode = 'r';
		
		//open file in readmode
		$this->handle = fopen( $this->file, $mode );
		
		if( $this->handle === false )
			die( 'Error opening the file ' . $file );
		
		//echo "...starting reading line by line... <br>"; //debugging line
		
		$row = 0;
		// read the data
		
		//echo "starting from row: " . $row . "<br><br>";
		
		//$data = [];
		
		//echo "the file contains: <br><br>";
		
		echo '<table class="table">';
		echo '<caption><b><a href="'.$this->file.'" download>Download CSV File</a></b></caption>';

		
		
		//foreach( $this->handle as $line )
		while( $data =  fgetcsv( $this->handle, null, ";" ) )
		{
			if( $row == 0 )
				echo "<thead>";
			//$data = str_fgetcsv( $line );
			echo '<tr>';
			//echo "row: " . strval( $row ) . ",data: " . implode( ",", $data ) . "<br>";
			echo '<td>'.implode( "</td><td>", $data ).'</td><td>';
			echo '</tr>';
			if( $row == 0 )
				echo '</thead><tbody>';
				
			$row++;
		}
		echo '</tbody></table>';
		
		//echo "<br> end of file, total lines read: " . strval(  $row ). "<br><br>";

		//close the file
		fclose( $this->handle );
	}
	
	public function __destruct ( )
	{
		if( is_resource( $this->handle ) )
			$this->close();
	}

}

?>