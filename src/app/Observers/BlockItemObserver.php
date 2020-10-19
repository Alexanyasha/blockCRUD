<?php

namespace Backpack\BlockCRUD\app\Observers;

use Backpack\BlockCRUD\app\Models\BlockItem;
use Storage;

class BlockItemObserver
{
    public function saved(BlockItem $block)
    {
        if($block->type == 'template' && empty($block->content)) {
            try {
            
                $block->content = file_get_contents(view($block->model_id)->getPath());
            
            } catch (\Exception $e) {
                logger($e->getMessage());
            }
        }

        if($block->type == 'template' && request()->has('code_preview')) {
            try {
                
                $block->html_content = request()->code_preview;
            
            } catch (\Exception $e) {
                logger($e->getMessage());
            }
        }
    }
}
