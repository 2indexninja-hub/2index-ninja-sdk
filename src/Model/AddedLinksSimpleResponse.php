<?php

namespace TwoIndexNinja\Sdk\Model;

class AddedLinksSimpleResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly string $projectName,
        public readonly int $projectId
    ) {}
}