<?php

namespace Drupal\guestbook\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for adding cats.
 */
class GuestBookForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): GuestBookForm {
    $instance = parent::create($container);
    $instance->messenger = $container->get('messenger');
    $instance->database = $container->get('database');
    return $instance;
  }

  /**
   * Drupal\Core\Database defenition.
   *
   * @var \Drupal\Core\Database\Connection|object|null
   */
  public $database;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'guestbook_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#prefix'] = '<div id="wrapper">';
    $form['#suffix'] = '</div>';
    $form['#attached'] = ['library' => ['guestbook/guestbook_library']];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your revue name:'),
      '#required' => TRUE,
      '#placeholder' => $this->t('Enter your name'),
      '#attributes' => [
        'class' => ['class_name'],
      ],
    ];
    $form['email'] = [
      '#type' => 'email',
      '#pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
      '#title' => $this->t('Your email:'),
      '#placeholder' => $this->t('Enter your email'),
      '#required' => TRUE,
      '#attributes' => [
        'class' => ['class_email'],
      ],
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#pattern' => '(\+?( |-|\.)?\d{1,2}( |-|\.)?)?(\(?\d{3}\)?|\d{3})( |-|\.)?(\d{3}( |-|\.)?\d{4})',
      '#required' => TRUE,
      '#title' => $this->t('Your phone number:'),
      "#placeholder" => $this->t('Enter your phone number'),
      '#attributes' => [
        'class' => ['class_phone'],
      ],
    ];
    $form['textmessage'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message:'),
      '#placeholder' => $this->t('Type here your message...'),
      "#required" => TRUE,
      '#attributes' => [
        'class' => ['class_textarea'],
      ],
    ];
    $form['avatar'] = [
      '#title' => $this->t('Your avatar:'),
      '#description' => $this->t('Add your photo'),
      '#type' => 'managed_file',
      '#size' => 40,
      '#attributes' => [
        'class' => ['class_avatar'],
      ],
      '#upload_location' => 'public://ava',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
    ];
    $form['image'] = [
      '#title' => $this->t('Your revue image:'),
      '#description' => $this->t('Add image to revue'),
      '#type' => 'managed_file',
      '#size' => 40,
      '#attributes' => [
        'class' => ['class_photo'],
      ],
      '#upload_location' => 'public://img',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [5242880],
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('ADD REVUE'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => ['class_submit'],
      ],
      '#ajax' => [
        'callback' => '::setMessage',
        'wrapper' => 'wrapper',
        'progress' => [
          'type' => 'none',
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Exception
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $ava = $form_state->getValue(['avatar']);
    $addedimg = $form_state->getValue(['image']);
    if ($ava !== NULL) {
      $ava_file = File::load($ava[0]);
      if($ava_file !== NULL) {
        $ava_file->setPermanent();
        $ava_file->save();
      }
    }
    if ($addedimg !== NULL) {
      $img_file = File::load($ava[0]);
      if($img_file !== NULL) {
        $img_file->setPermanent();
        $img_file->save();
      }
    }
      $this->database
        ->insert('guestbook')
        ->fields([
          'name' => $form_state->getValue(['name']),
          'email' => $form_state->getValue(['email']),
          'phone' => $form_state->getValue(['phone']),
          'textmessage' => $form_state->getValue(['textmessage']),
          'avatar' => $form_state->getValue(['avatar'])[0],
          'image' => $form_state->getValue(['image'])[0],
          'date' => time(),
        ])
        ->execute();
    $this->messenger->addStatus($this->t('Hurray! You added your revue! Refresh the page!'));
  }

  /**
   * Form validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if ((mb_strlen($form_state->getValue('name')) < 2)) {
      $form_state->setErrorByName(
        'name',
        $this->t('Your name is less than 2 symbols.'));
    }
    if ((mb_strlen($form_state->getValue('name')) > 100)) {
      $form_state->setErrorByName(
        'name',
        $this->t('Your name is longer than 100 symbols.')
      );
    }
  }

  /**
   * Submit Ajax.
   */
  public function setMessage(array &$form, FormStateInterface $form_state) : array {
    return $form;
  }

}
