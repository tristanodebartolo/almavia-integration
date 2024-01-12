<?php
namespace Drupal\almavia_integration\Commands;

// use Drupal\almavia_integration\Service\Resalys;
use Drush\Commands\DrushCommands;
use Drupal\Core\File\FileSystemInterface;

class CreatePageIntegrationCommands extends DrushCommands {

  /**
   * Init a page for integration
   *
   * @command almavia:add-page
   * @usage drush almavia:add-page
   * @aliases add-page
   */
  public function createPage() {

    $text = $this->io()->ask('Please enter a page title. (Ex : Contact)');

    if ($text) {

      /** @var \Drupal\pathauto\AliasCleaner $aliasCleaner */
      $aliasCleaner = \Drupal::service("pathauto.alias_cleaner");
      $text_clean = $aliasCleaner->cleanString((string)$text);

      /** @var \Drupal\Core\File\FileSystemInterface $file_system */
      $file_system = \Drupal::service('file_system');

      /** @var \Drupal\Core\Extension\ExtensionList $extension_list */
      $extension_list = \Drupal::service('extension.list.module');
      $file_modele_directory = $extension_list->getPath('almavia_integration');

      // Create json file
      $file_data = $file_modele_directory . '/modeles/data.json';
      $directory_data = $file_modele_directory . '/data';

      $file_system->prepareDirectory(
        $directory_data,
        FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS
      );

      $new_file_data = $directory_data . '/' . $text_clean .'.json';
      $file_system->copy($file_data, $new_file_data, FileSystemInterface::EXISTS_REPLACE);

      $formated_template = str_replace('-', '_', $text_clean);
      $str_replace = file_get_contents($new_file_data);
      $str_replace = str_replace('name', $text, $str_replace);
      $str_replace = str_replace('tpl', $formated_template, $str_replace);
      file_put_contents($new_file_data, $str_replace);

      // Create twig file
      $file_template = $file_modele_directory . '/modeles/almavia--page.html.twig';
      $directory_template = $file_modele_directory . '/templates';

      $file_system->prepareDirectory(
        $directory_template,
        FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS
      );

      $new_file_template = $directory_template . '/almavia--integration--'.$text_clean.'.html.twig';
      $file_system->copy($file_template, $new_file_template, FileSystemInterface::EXISTS_REPLACE);

      drupal_flush_all_caches();

      $this->io()->progressStart(2);
      for ($i = 0; $i < 2; $i++) {
        $this->io()->progressAdvance();
        sleep(1);
      }

      $this->io()->progressFinish();
      $this->io()->success(t('Both files have been created. The caches have been emptied.'));

      $this->io()->table(
        [ 'Type', 'File' ],
        [
          ['json', $new_file_data],
          ['twig', $new_file_template],
        ]
      );

    }else {
      $this->io()->info('Almavia intégration page');
      $this->io()->error('Please enter a page title :(');
    }

  }

}
