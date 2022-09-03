<?php

namespace App\Admin;

use App\Entity\MavenRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MavenRepositoryGroupAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->with('Basic Information');
        if ($this->isCurrentRoute('create')) {
            $form->add(
                'shortName',
                null,
                ['help' => 'Cannot be changed after creation. Lowercase letters, numbers, dashes and underscores only.']
            );
        }
        $form->add('name');
        $form->add('visible', null, ['required' => false]);
        $form->end();

        $form->with('Repositories');
        $form->add('mavenRepositories');
        $form->end();

        $form->with('Permissions');
        $form->add('readUsers');
        $form->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->add('shortName');
        $list->add('name');
        $list->add('visible');

        $list->add(ListMapper::NAME_ACTIONS, null, ['actions' => ['edit' => [], 'delete' => []]]);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object): string
    {
        return $object instanceof MavenRepository ? $object->name : 'RepositoryGroup';
    }
}
