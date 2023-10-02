<?php

namespace ShoppingFeed\Sdk\Api\Order;

class UploadOrderDocumentCollection
{
    /** @var array  */
    private $documents = [];

    public function addDocument(UploadOrderDocument $document): void
    {
        $this->documents[] = $document;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }
}
