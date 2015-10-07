<?php

/**
 * @file
 * template.php
 */

function bootstrap_form_user_register_form_alter(&$form, &$form_state, $form_id){
	$form['account']['name']['#title']=t('Benutzername');
	$form['account']['mail']['#description']=t('Email');
	$form['account']['mail']['#description']=t('Bitte nutzen sie eine gültige Emailaddresse');
}

// Platzhaltertext fuer Suchformular aendern
function bootstrap_form_search_block_form_alter( &$form, &$form_state, $form_id ) {
	$form['search_block_form']['#attributes']['placeholder'] = t('Suche im Forum');	
}
?>