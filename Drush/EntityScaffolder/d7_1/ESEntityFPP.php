<?php

namespace Drush\EntityScaffolder\d7_1;

use Drush\EntityScaffolder\Utils;
use Drush\EntityScaffolder\d7_1\ESBaseInterface;
use Drush\EntityScaffolder\d7_1\ESEntity;
use Drush\EntityScaffolder\Logger;

class ESEntityFPP extends ESEntity implements ESBaseInterface {

  public function help() {
    Logger::log('[fpp] : Fieldable Panels Pane', 'status');
    Logger::log('Following are the values supported in configuration', 'status');
    $headers = array('Property', 'Variable type', 'Description');
    $data = [];
    $data[] = ['name', 'string' ,'The label of the fpp, which is displayed to the editors.'];
    $data[] = ['machine_name', 'machine name (string)' , 'Internal machine name.'];
    $data[] = ['fields', 'array' ,'Array of field definitions that is attached to the entity'];
    Logger::table($headers, $data, 'status');
  }

  public function __construct(Scaffolder $scaffolder) {
    parent::__construct($scaffolder);
    $this->plugins['field_base'] = new ESFieldBase($this->scaffolder);
    $this->plugins['field_instance'] = new ESFieldInstance($this->scaffolder);
    $this->plugins['preprocess'] = new ESFieldPreprocess($this->scaffolder);
    $this->plugins['patternlab_template_manager'] = new ESPatternLabField($this->scaffolder);
    $this->setTemplateDir('/entity/fpp');
  }

  public function generateCode($info) {
    $module = 'fe_es';
    $filename = 'fe_es.fieldable_panels_pane_type.inc';
    // Add File header.
    $block = Scaffolder::HEADER;
    $key = 0;
    $template = '/entity/fpp/feature.header';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add Code block.
    $block = Scaffolder::CONTENT;
    $key = $info['machine_name'];
    $template = '/entity/fpp/feature.content';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add file footer.
    $block = Scaffolder::FOOTER;
    $key = 0;
    $template = '/entity/fpp/feature.footer';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

    // Add entry to info file.
    $code = "\nfeatures[fieldable_panels_pane_type][] = {$info['machine_name']}";
    $module = 'fe_es';
    $filename = 'fe_es.info';
    $block = Scaffolder::CONTENT;
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[ctools][] = fieldable_panels_panes:fieldable_panels_pane_type:1";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    $code = "\nfeatures[features_api][] = api:2";
    $this->scaffolder->setCode($module, $filename, $block, $code, $code);
    if (!empty($info['local_config']['dependencies'])) {
      foreach ($info['local_config']['dependencies'] as $dependency) {
        $code = "\ndependencies[] = {$dependency}";
        $this->scaffolder->setCode($module, $filename, $block, $code, $code);
      }
    }

    $module = 'fe_es';
    $filename = 'fe_es.features.inc';
    $block = Scaffolder::CONTENT;
    $key = 'ctools_plugin_api : ' . Scaffolder::CONTENT . ' : fpp';
    $template = '/entity/fpp/features.inc.ctools_plugin_api';
    $code = $this->scaffolder->render($template, $info);
    $this->scaffolder->setCode($module, $filename, $block, $key, $code);

  }

  /**
   * Loads scaffold source files.
   */
  public function loadScaffoldSourceConfigurations() {
    return Utils::getConfigFiles($this->scaffolder->getConfigDir() . '/fpp');
  }

}
