<?php
namespace SlimApi\Generator;

interface GeneratorInterface
{
    /**
     * Validate names and fields provided
     *
     * @param string $name
     * @param array $fields
     *
     * @return bool are name/fields valid
     */
    public function validate($name, $fields);

    /**
     * Process the fields as required
     *
     * @param string $name
     * @param array $fields
     * @param array $options
     *
     * @return void
     */
    public function process($name, $fields, $options);
}
