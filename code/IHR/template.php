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
/*
function bootstrap_user_register_form_submit($form, &$form_state){
    $edit = array(
          'name' => $form_state['values']['name'], 
          'pass' => user_password(),
          'mail' => $form_state['values']['mail'],
          'init' => $form_state['values']['mail'], 
          'status' => 1, 
          'access' => REQUEST_TIME,
          'field_vorname' => array(LANGUAGE_NONE => array(array('value' => $form_state['values']['field_fname']))),
          'field_nachname' => array(LANGUAGE_NONE => array(array('value' => $form_state['values']['field_lname']))),
    );
    user_save(drupal_anonymous_user(), $edit);
}
*/