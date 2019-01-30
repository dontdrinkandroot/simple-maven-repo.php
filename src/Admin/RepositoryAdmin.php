<?php

namespace App\Admin;

use App\Entity\Repository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class RepositoryAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        if ($this->isCurrentRoute('create')) {
            $form->add(
                'shortName',
                null,
                ['help' => 'Cannot be changed after creation. Lowercase letters, numbers, dashes and underscores only.']
            );
        }
        $form->add('name');
        $form->add('visible', null, ['required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list->add('shortName');
        $list->add('name');
        $list->add('visible');

        $list->add('_action', null, ['actions' => ['edit' => [], 'delete' => []]]);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object instanceof Repository ? $object->getName() : 'Repository';
    }
}
