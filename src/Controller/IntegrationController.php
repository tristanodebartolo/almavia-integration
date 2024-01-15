<?php

namespace Drupal\almavia_integration\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Controller for switch and back to masquerade as user.
 */
class IntegrationController extends ControllerBase {

  /**
   * Callback for slice page.
   */
  public function page($id) {

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('almavia_integration')->getPath();

    $fileName = $module_path . '/data/' . $id . '.json';
    $data = file_get_contents($fileName);
    $data = Json::decode($data);

    return [
      '#theme' => 'almavia__integration__' . $data['template'],
      '#page' => $data,
      '#module_path' => $module_path,
    ];

  }

  /**
   * Callback for slice page.
   */
  public function list() {

    // get path of module almavia_integration
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('almavia_integration')->getPath();

    // Find each file data json
    $fileSystem = \Drupal::service('file_system');
    $templates = $fileSystem->scanDirectory($module_path. '/data', '/json$/');
    $li_link = [];

    // if files data json
    if (is_array($templates) && count($templates) > 0) {

      // loop file data json
      foreach ($templates as $template) {

        // get template data
        $data = file_get_contents($template->uri);
        $data = Json::decode($data);

        // build url page
        $template_name = $template->name;
        $url_object = Url::fromRoute('almavia_integration.page', ['id' => $template_name]);

        // build link render
        $li_link[] = [
          '#type' => 'html_tag',
          '#tag' => 'li',
          'link' => [
            '#type' => 'link',
            '#url' => $url_object,
            '#title' => $data['title'],
          ]
        ];

      }

    }

    // retour render array page
    return [
      '#type' => 'container',
      'ol' => [
        '#type' => 'html_tag',
        '#tag' => 'ol',
        'li' => $li_link
      ]
    ];

  }


  /**
   * @param \Drupal\Core\Session\AccountInterface $user
   * @param $id
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access(AccountInterface $user, $id):AccessResult {

    $cache_builder = ['user:'.$user->id()];

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('almavia_integration')->getPath();

    // File page
    $fileName = $module_path . '/data/' . $id . '.json';

    if (($data = @file_get_contents($fileName)) === false) {
      return AccessResult::forbidden()->addCacheableDependency($cache_builder)->cachePerUser();
    }else {
      return AccessResult::allowed()->addCacheableDependency($cache_builder)->cachePerUser();
    }

  }

}
