<?php

namespace IandePlugin;

use Controller;

class Filter extends Controller {

    function endpoint_list (array $params = []) {
        $post_type = $params['post_type'];

        if ($post_type === 'group') {
            $metadata_definition = \call_user_func('IandePlugin\\get_all_group_metadata_definition');
        } else {
            $metadata_definition = \call_user_func('IandePlugin\\get_' . $post_type . '_metadata_definition');
        }

        $filters = [];

        foreach ($metadata_definition as $field => $definition) {
            $metabox = $definition->metabox;

            if (!empty($metabox) && !empty($metabox->name)) {
                $filter = [
                    'id' => $field,
                    'name' => $metabox->name,
                    'type' => $metabox->type,
                ];

                if (isset($metabox->options) && $field !== 'city') {
                    $filter['options'] = $metabox->options;
                }

                $filters[] = $filter;
            }
        }

        $this->success($filters);
    }
}
