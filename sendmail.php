<?php

define('TO_EMAIL', 'info@bauldebarrio.co, lineaprimitiva@gmail.com'); 
define('FROM_EMAIL', 'email de respuesta');  
define('FROM_NAME', 'Usuario sitio Web'); 


define( 'BODY', '%message%<br /><br /><small>Correo enviado por %name%, email %email%.</small>' );
define( 'SUBJECT', 'Correo desde el sitio web' );


define( 'ERROR_URL', 'contatti_error.html' );
define( 'SUCCESS_URL', 'contatti_success.html' ); 
define( 'NOTSENT_URL', 'contatti_notsent.html' );           


$msg = array(
    'error' => '<p class="error">Advertencia! Ingresa correctamente los datos indicados</p>',
    'success' => '<p class="success">Su correo fue enviado correctamente. Gracias!</p>',
    'not-sent' => '<p class="error">Ha ocurrido un error, por favor intente de nuevo.</p>'
);      

$required = array( 'name', 'email', 'message' );


sendemail();
    

function sendemail() 
{
    global $msg, $required;
    
    if ( isset( $_POST['ajax'] ) )
        $ajax = $_POST['ajax'];
    else
        $ajax = false;
    
	if ( isset( $_POST['action'] ) AND $_POST['action'] == 'sendmail' ) 
	{
	    $body = BODY;
	    
	    $post_data = array_map( 'stripslashes', $_POST );
	    

	    
	    foreach ( $required as $id_field ) {
    	    if( $post_data[$id_field] == '' || is_null( $post_data[$id_field] ) ) {
    	        if ( $ajax )
    	           end_ajax( $msg['error'] );
    	        else
    	    	   redirect( ERROR_URL );
    	    }                       
    	}
	    
	    if( !is_email( $post_data['email'] ) OR $post_data['email'] == '' ) 
	        if ( $ajax )
	           end_ajax( $msg['error'] );
	        else
    	       redirect( ERROR_URL );
	    
	    foreach( $post_data as $id => $var )
	    {
	    	if( $id == 'message' ) $var = nl2br($var);
			$body = str_replace( "%$id%", $var, $body );	
		}
	    
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'From: '.FROM_NAME.' <'.FROM_EMAIL.'>' . "\r\n" . 'Reply-To: ' . $post_data['email'];
	
	    $sendmail = mail( TO_EMAIL, SUBJECT, $body, $headers );
	         
		if ( $sendmail ) 
	        if ( $ajax )
	           end_ajax( $msg['success'] );
	        else
    	       redirect( SUCCESS_URL );
	    else
	        if ( $ajax )
	           end_ajax( $msg['not-sent'] );
	        else
    	       redirect( NOTSENT_URL );
	} 
}

function is_email($email) 
{
    if (!preg_match("/[a-z0-9][_.a-z0-9-]+@([a-z0-9][0-9a-z-]+.)+([a-z]{2,4})/" , $email))
    {
        return false;
    }
    else
    {
        return true;
    }
}             

function end_ajax( $msg = '' ) {
    echo $msg;
    die;
}           

function redirect( $redirect = '' ) {
    header( 'Location: ' . $redirect );
    die;
}      

?>
