<?php

namespace Entities\Users\Components\Vue\UserWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ManageUser extends VueComponent
{
    protected string $id = "b95a2a19-7fc6-4104-8528-728ee0584f92";

    public function buildTemplate() : string
    {
        return 'const ' . $this->getInstanceName().' = {
            name: \'Excell Component\',
            props: { '.
            (implode(", ", $this->buildPropsJavaScriptObject()))
            .' },
            data: function() {
                return {
                    self: null,
                };
            },
            template: `
    <div>Hello World</div>  `,
            mounted() {
                
            },
            methods: {
                
            }
        };';
    }
}