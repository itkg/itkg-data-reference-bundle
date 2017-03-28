<?php

namespace Itkg\ReferenceApiBundle\Exceptions\HttpException;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class ReferenceNotFoundHttpException
 */
class ReferenceNotFoundHttpException extends ApiException
{
    const DEVELOPER_MESSAGE = 'itk_reference_api.reference.not_found';
    const HUMAN_MESSAGE     = 'itk_reference_api.reference.not_found';
    const STATUS_CODE       = '404';
    const ERROR_CODE        = 'x';

    /**
     * Constructor
     */
    public function __construct()
    {
        $developerMessage = self::DEVELOPER_MESSAGE;
        $humanMessage     = self::HUMAN_MESSAGE;
        $statusCode       = self::STATUS_CODE;
        $errorCode        = self::ERROR_CODE;

        parent::__construct($statusCode, $errorCode, $developerMessage, $humanMessage);
    }
}
