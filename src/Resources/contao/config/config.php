<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 */

use Trilobit\ContaoMultiFormGroup\Form\FormCompiler;

$GLOBALS['TL_HOOKS']['compileFormFields'][] = [FormCompiler::class, 'onCompileFormFields'];

$request = Contao\System::getContainer()
    ->get('request_stack')
    ->getCurrentRequest()
;
if ($request && !Contao\System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
    Contao\System::loadLanguageFile('default');

    $GLOBALS['TL_HEAD'][] = '<script>var multi_form_control_add_label = "'.$GLOBALS['TL_LANG']['MSC']['multi_form_control_add'].'";</script>';
    $GLOBALS['TL_HEAD'][] = '<script>var multi_form_control_remove_label = "'.$GLOBALS['TL_LANG']['MSC']['multi_form_control_remove'].'";</script>';
    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/trilobitcontaomultiformgroup/js/frontend.js';
}
