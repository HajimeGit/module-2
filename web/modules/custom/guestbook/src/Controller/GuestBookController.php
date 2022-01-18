<?php

namespace Drupal\guestbook\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\guestbook\Form\GuestBookForm;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Revues controller.
 */
class GuestBookController extends ControllerBase {

  /**
   * Drupal\Core\Database defenition.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * Drupal\Core\Form\FormBuilder definition.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): GuestBookController {
    $instance = parent::create($container);
    $instance->formBuilder = $container->get('form_builder');
    $instance->database = $container->get('database');
    return $instance;
  }

  /**
   * Getting form from GuestBookForm.
   */
  public function content(): array {
    $form = $this->formBuilder()->getForm(GuestBookForm::class);
    $revues[] = $this->outputting();
    return [
      '#theme' => 'guestbook-theme',
      '#form' => $form,
      '#revues' => $revues,
    ];
  }

  /**
   * Outputting table with revues on the page.
   */
  public function outputting(): array {
    $table_result = $this->database->select('guestbook', 'gb')
      ->fields('gb', ['id', 'name', 'email', 'phone', 'textmessage', 'avatar', 'image', 'date'])
      ->orderBy('id', 'DESC')
      ->execute();
    $revues = [];
    foreach ($table_result as $revue) {
      if($revue->avatar !== NULL) {
      $uriava = File::load($revue->avatar);
      if($uriava !== NULL) {
          $revue->avatar = [
            '#theme' => 'image_style',
            '#style_name' => 'wide',
            '#uri' => $uriava->getFileUri(),
            '#attributes' => [
              'class' => 'revue_avatar_img',
              'alt' => 'avatar image',
            ],
          ];
        }
      }
      else {
        $revue->avatar = [
          '#theme' => 'image',
          '#uri' => 'sites/default/files/img/default/default_image.png',
          '#attributes' => [
            'class' => 'revue_avatar_img',
            'alt' => 'avatar image',
          ],
        ];
      }
      if($revue->image !== NULL) {
        $uriimg = File::load($revue->image);
        if($uriimg !== NULL) {
          $revue->image = [
            '#theme' => 'image_style',
            '#style_name' => 'wide',
            '#uri' => $uriimg->getFileUri(),
            '#attributes' => [
              'class' => 'revue_image_itself',
              'alt' => 'revue image',
            ],
          ];
        }
      }
      $render_revue = [
        '#theme' => "guestbook-table-block",
        '#name' => $revue->name,
        '#email' => $revue->email,
        '#phone' => $revue->phone,
        '#textmessage' => $revue->textmessage,
        '#avatar' => $revue->avatar,
        '#image' => $revue->image,
        '#date' => date('d-m-Y H:i:s', $revue->date),
        '#id' => $revue->id,
      ];
      $revues[] = $render_revue;
    }
    return $revues;
  }

}
