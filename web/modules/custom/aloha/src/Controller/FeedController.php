<?php

namespace Drupal\aloha\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An example controller.
 */
class FeedController extends ControllerBase {

  /**
   * Drupal\Core\Database\ definition.
   *
   * @var \Drupal\Core\Database\
   */
  public $database;

  /**
   * Dependency Injection.
   */
  public static function create(ContainerInterface $container): FeedController {
    $instanse = parent::create($container);
    $instanse->database = $container->get('database');
    $instanse->formBuilder = $container->get('form_builder');
    return $instanse;
  }

  /**
   * Returns a render-able array for a test page.
   */
  public function content(): array {
    $form = $this->formBuilder()->getForm('Drupal\aloha\Form\FeedForm');
    $feedback[] = $this->outputCats();
    $build['content'] = [
      '#theme' => 'aloha-page',
      '#form' => $form,
      '#feedback' => $feedback,
    ];
    return $build;
  }

  /**
   * Outputting table with response on the page.
   */
  public function outputCats(): array {
    $table_result = $this->database->select('aloha', 'alo')
      ->fields('alo', [
        'id',
        'name',
        'tel',
        'email',
        'image',
        'avatar',
        'text',
        'date',
      ])
      ->orderBy('id', 'DESC')
      ->execute()
      ->fetchAll();
    $feedback = [];
    foreach ($table_result as $feed) {
      if ($feed->avatar !== NULL) {
        $feed->avatar = [
          '#theme' => 'image_style',
          '#style_name' => 'avatars',
          '#uri' => File::load($feed->avatar)->getFileUri(),
          '#attributes' => [
            'class' => 'avatar_image',
            'alt' => 'cat photo',
          ],
        ];
      }
      else {
        $feed->avatar = [
          '#theme' => 'image_style',
          '#style_name' => 'avatars',
          '#uri' => 'guest.png',
          '#attributes' => [
            'class' => 'avatar_image',
            'alt' => 'avatar image',
          ],
        ];
      }
      if ($feed->image !== NULL) {
        $feed->image = [
          '#theme' => 'image_style',
          '#style_name' => 'response',
          '#uri' => File::load($feed->image)->getFileUri(),
          '#attributes' => [
            'class' => 'response_image',
            'alt' => 'response image',
          ],
        ];
      }
      $response = [
        '#theme' => 'response-table-block',
        '#name' => ($feed->name),
        '#tel' => $feed->tel,
        '#email' => $feed->email,
        '#image' => $feed->image,
        '#avatar' => $feed->avatar,
        '#text' => $feed->text,
        '#date' => date('d-m-Y H:i:s', $feed->date),
        '#id' => $feed->id,
        '#delete' => $this->t('Delete'),
        '#edit' => $this->t('Edit'),
      ];
      $feedback[] = $response;
    }
    return $feedback;
  }

}
