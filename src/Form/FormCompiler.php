<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-multiformgroup-bundle
 */

namespace Trilobit\ContaoMultiFormGroup\Form;

use Contao\Form;
use Contao\FormFieldModel;
use Contao\Input;

class FormCompiler
{
    /**
     * @param FormFieldModel[] $fields
     * @param string           $formFieldId
     * @param Form             $form
     *
     * @return FormFieldModel[]
     */
    public function onCompileFormFields(array $fields, $formFieldId, $form)
    {
        $offset = 0;
        $indexFrom = 0;
        $level = 0;

        foreach (array_values($fields) as $index => $fieldModel) {
            if (('fieldset' === $fieldModel->type && 'fsStart' === $fieldModel->fsType) || 'fieldsetStart' === $fieldModel->type) {
                // ignore fieldsets in front of and after the multi form groups
                if (0 === $level && !$fieldModel->multi_form_group) {
                    continue;
                }

                if ($fieldModel->multi_form_group) {
                    $indexFrom = $index;
                }
                ++$level;

                continue;
            }

            if (('fieldset' === $fieldModel->type && 'fsStop' === $fieldModel->fsType) || 'fieldsetStop' === $fieldModel->type) {
                --$level;
                if (0 === $level) {
                    $length = $index - $indexFrom + 1;
                    $multipliedFields = $this->multiplyFields(
                        \array_slice($fields, $offset + $indexFrom, $length)
                    );

                    // remove existing fields and replace them with the new fields
                    array_splice($fields, $offset + $indexFrom, $length, $multipliedFields);

                    // todo: check if offset is correct (test case: multiple multi-form-groups on the same form)
                    $offset += \count($multipliedFields);
                }

                continue;
            }
        }

        return $fields;
    }

    /**
     * @param $fields array The fields [<, A, B, C, >] to multiply (first and last element must be a fieldset)
     *
     * @return array an array containing the multiplied sequence with adapted identifiers
     *               [<, A1, B1, C1, >, <, A2, B2, C2, >, <, A3, B3, C3, >]
     */
    private function multiplyFields($fields)
    {
        $groupId = $fields[0]->id;

        // tag first field
        $fields[0]->class = 'multi_form_group multi_form_group__'.$groupId;

        $groupCount = $this->getGroupCount($groupId);

        $multipliedFields = [];
        for ($i = 0; $i < $groupCount; ++$i) {
            foreach ($fields as $field) {
                $multipliedField = clone $field;

                // add suffix
                $multipliedField->name = $this->getSuffixedIdentifier($field->name, (string) $i);
                $multipliedField->id = $this->getSuffixedIdentifier($field->name, (string) $i);

                $multipliedFields[] = $multipliedField;
            }
        }

        return $multipliedFields;
    }

    /**
     * Return the number of groups to be shown.
     *
     * @param $groupId
     *
     * @return int
     */
    private function getGroupCount($groupId)
    {
        //$mandatoryFields = array_filter($fields, function ($field) {
        //    return $field->mandatory;
        //});
        $size = (int) Input::post('multi_form_size__'.$groupId);

        return max($size, 1);
    }

    /**
     * Helper: Generate the suffixed identifier.
     *
     * @param $name
     * @param $groupSuffix
     *
     * @return string
     */
    private function getSuffixedIdentifier($name, $groupSuffix)
    {
        return $name.'__'.$groupSuffix;
    }
}
