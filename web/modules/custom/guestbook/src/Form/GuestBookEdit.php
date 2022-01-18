<?php

namespace Drupal\guestbook\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for editing revues.
 */
class GuestBookEdit extends FormBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): GuestBookEdit {
    $instance = parent::create($container);
    $instance->messenger = $container->get('messenger');
    $instance->database = $container->get('database');
    return $instance;
  }

  /**
   * Revues ids storaging.
   *
   * @var null
   */
  public $id;

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
    return 'guestbook_edit';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL): array {
    $this->id = $id;
    $data = $this->database->select('guestbook', 'gb')
      ->condition('gb.id', $id, '=')
      ->fields('gb', ['id', 'name', 'email', 'phone', 'textmessage', 'avatar', 'image', 'date'])
      ->execute()->fetchAll();
    $form['#prefix'] = '<div id="wrapper">';
    $form['#suffix'] = '</div>';
    $form['#attached'] = ['library' => ['guestbook/guestbook_library']];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your revue name:'),
      '#required' => TRUE,
      '#default_value' => $data[0]->name,
      '#placeholder' => $this->t('Enter your name'),
      '#attributes' => [
        'class' => ['class_name'],
      ],
    ];
    $form['email'] = [
      '#type' => 'email',
      '#pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
      '#title' => $this->t('Your email:'),
      '#default_value' => $data[0]->email,
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
      '#default_value' => $data[0]->phone,
      '#title' => $this->t('Your phone number:'),
      "#placeholder" => $this->t('Enter your phone number'),
      '#attributes' => [
        'class' => ['class_phone'],
      ],
    ];
    $form['textmessage'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message:'),
      '#default_value' => $data[0]->textmessage,
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
      '#default_value' => [$data[0]->avatar],
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
      '#default_value' => [$data[0]->image],
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
      '#value' => $this->t('Save'),
      '#button_type' => 'submit',
      '#ajax' => [
        'callback' => '::setMessage',
        'wrapper' => 'edit_wrapper',
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
    if ($ava[0] !== NULL) {
      $ava_file = File::load($ava[0]);
      if($ava_file !== NULL) {
        $ava_file->setPermanent();
        $ava_file->save();
      }
    }
    if ($addedimg[0] !== NULL) {
      $img_file = File::load($addedimg[0]);
      if($img_file !== NULL) {
        $img_file->setPermanent();
        $img_file->save();
      }
    }
      $this->database
        ->update('guestbook')
        ->condition('id', $this->id)
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
    $this->messenger->addStatus($this->t('Hurray! You edited!'));
    $form_state->setRedirect('guestbook');
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
  public function setMessage(array &$form, FormStateInterface $form_state) : AjaxResponse {
    $response = new AjaxResponse();
    $url = Url::fromRoute('guestbook');
    $command = new RedirectCommand($url->toString());
    $response->addCommand($command);
    return $response;
  }

}
