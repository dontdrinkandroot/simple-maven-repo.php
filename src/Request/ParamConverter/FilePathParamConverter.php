<?php

namespace App\Request\ParamConverter;

use Dontdrinkandroot\Path\FilePath;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class FilePathParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        try {
            $request->attributes->set(
                $configuration->getName(),
                FilePath::parse($request->attributes->get($configuration->getName()))
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return FilePath::class === $configuration->getClass();
    }
}