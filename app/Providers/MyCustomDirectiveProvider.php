<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use app\Http\Helpers\MyHelper;

class MyCustomDirectiveProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public $icons = [ 
                'trash'         => '<i class="fas fa-trash"></i>', 
                'trash-alt'     => '<i class="fas fa-trash-alt"></i>', 
                
                'create'        => '<i class="fas fa-plus-circle"></i>',
                'edit'          => '<i class="fas fa-edit"></i>',
                'delete'        => '<i class="fas fa-minus-circle"></i>',
                
                'plus-square'   => '<i class="fas fa-plus-square"></i>',
                'minus-square'  => '<i class="fas fa-minus-square"></i>',
                'plus-circle'   => '<i class="fas fa-plus-circle"></i>',
                'minus-circle'  => '<i class="fas fa-minus-circle"></i>',
                'pen'           => '<i class="fas fa-pen"></i>',
                'pen-alt'       => '<i class="fas fa-pen-alt"></i>',
                'pen-square'    => '<i class="fas fa-pen-square"></i>',

                'folder'           => '<i class="fas fa-folder"></i>',
                'folder-far'           => '<i class="far fa-folder"></i>',
                'folder-open'      => '<i class="fas fa-folder-open"></i>',
                'folder-open-far'      => '<i class="far fa-folder-open"></i>',
                
                'copyright'           => '<i class="fas fa-copyright"></i>',
                'copyright-far'           => '<i class="far fa-copyright"></i>',
                
                'exclamation'       => '<i class="fas fa-exclamation"></i>',
                'exclamation-triangle'       => '<i class="fas fa-exclamation-triangle"></i>',
                'exclamation-circle'       => '<i class="fas fa-exclamation-circle"></i>',
                
                'comment'           => '<i class="fas fa-comment"></i>',
                'comment-slash'           => '<i class="fas fa-comment-slash"></i>',
                'comment-medical'           => '<i class="fas fa-comment-medical"></i>',
                'comment-dots'           => '<i class="fas fa-comment-dots"></i>',
                'comments'           => '<i class="fas fa-comments"></i>',
                
                'google'        => '<i class="fab fa-google"></i>',

                'envelope'              => '<i class="fas fa-envelope"></i>',
                'envelope-square'       => '<i class="fas fa-envelope-square"></i>',
                'envelope-open-text'    => '<i class="fas fa-envelope-open-text"></i>',
                'envelope-open'         => '<i class="fas fa-envelope-open"></i>',
                
                
                
                'times'           => '<i class="fas fa-times"></i>',
                'times-circle'  => '<i class="fas fa-times-circle icon_btn"></i>',
                'window-close'  => '<i class="fas fa-window-close"></i>',
                'slash'           => '<i class="fas fa-slash"></i>',
                'store-slash'           => '<i class="fas fa-store-slash"></i>',
                'store-alt-slash'           => '<i class="fas fa-store-alt-slash"></i>',

                
                
                'info'          => '<i class="fas fa-info"></i>',
                'info-circle'   => '<i class="fas fa-info-circle"></i>',
                'question-circle'          => '<i class="fas fa-question-circle"></i>',
                'question'          => '<i class="fas fa-question"></i>',
                
                'copy'          => '<i class="fas fa-copy"></i>',
                'paste'         => '<i class="fas fa-paste"></i>',
                'cut'           => '<i class="fas fa-cut"></i>',
                
                'clipboard'     => '<i class="fas fa-clipboard"></i>',
                'clipboard-far'     => '<i class="far fa-clipboard"></i>',
                
                'sync'          => '<i class="fas fa-sync"></i>',
                'sync-alt'      => '<i class="fas fa-sync-alt"></i>',

                'code'          => '<i class="fas fa-code"></i>',
                
                'dev'           => '<i class="fab fa-dev"></i>',
                'develop'       => '<i class="fab fa-dev"></i>',
                'dump'          => '<i class="fab fa-deploydog"></i>',
                'deploydog'     => '<i class="fab fa-deploydog"></i>',
                'debug'         => '<i class="fab fa-dev text-danger btn_icon"></i>',
                
                'desktop'            => '<i class="fas fa-desktop"></i>',
                'mobile'        => '<i class="fas fa-mobile"></i>',
                'mobile-alt'    => '<i class="fas fa-mobile-alt"></i>',
                'database'       => '<i class="fas fa-database"></i>',
                
                'eye'           => '<i class="fas fa-eye"></i>',
                
                'lock'          => '<i class="fas fa-lock"></i>',
                'unlock'        => '<i class="fas fa-unlock"></i>',
                
                'search'        => '<i class="fas fa-search"></i>',
                'zoomin'        => '<i class="fas fa-search-plus"></i>',
                'zoomout'       => '<i class="fas fa-search-minus"></i>',

                'address-card'  => '<i class="fas fa-address-card"></i>',
                'address-book'  => '<i class="fas fa-address-book"></i>',
                
                'schedule'      => '<i class="far fa-calendar"></i>',
                'calendar'      => '<i class="far fa-calendar-alt"></i>',

                'clipboard-list'     => '<i class="fas fa-clipboard-list"></i>',
                'tasks'         => '<i class="fas fa-tasks"></i>',
                'list'          => '<i class="fas fa-list"></i>',
                'list-ul'       => '<i class="fas fa-list-ul"></i>',
                'list-ol'       => '<i class="fas fa-list-ol"></i>',
                'list-alt'      => '<i class="fas fa-list-alt"></i>',
                
                'check'         => '<i class="fas fa-check"></i>',
                'check-square'  => '<i class="fas fa-check-square"></i>',
                'check-circle'  => '<i class="fas fa-check-circle"></i>',
                'good'          => '<i class="fas fa-thumbs-up"></i>',
                'bad'           => '<i class="fas fa-thumbs-down"></i>',
                
                
                'config'        => '<i class="fas fa-cog"></i>',
                'bars'          => '<i class="fas fa-bars"></i>',

                'download'          => '<i class="fas fa-download"></i>',
                'upload'            => '<i class="fas fa-upload"></i>',
                'file-download'     => '<i class="fas fa-file-download"></i>',
                'file-upload'       => '<i class="fas fa-file-upload"></i>',
                'cloud-upload'      => '<i class="fas fa-cloud-upload-alt"></i>',
                'cloud-download'    => '<i class="fas fa-cloud-download-alt"></i>',
                
                'clone'            => '<i class="fas fa-clone"></i>',
                'clone-far'        => '<i class="far fa-clone"></i>',

                
                'circle-right'  => '<i class="fas fa-arrow-alt-circle-right"></i>',
                'circle-left'   => '<i class="fas fa-arrow-alt-circle-left"></i>',
                'circle-up'     => '<i class="fas fa-arrow-alt-circle-up"></i>',
                'circle-down'   => '<i class="fas fa-arrow-alt-circle-down"></i>',
                
                'caret-right'  => '<i class="fas fa-caret-right"></i>',
                'caret-left'   => '<i class="fas fa-caret-left"></i>',
                'caret-up'     => '<i class="fas fa-caret-up"></i>',
                'caret-down'   => '<i class="fas fa-caret-down"></i>',
                
                'arrow-right'   => '<i class="fas fa-arrow-right"></i>',
                'arrow-left'   => '<i class="fas fa-arrow-left"></i>',
                'arrow-up'   => '<i class="fas fa-arrow-up"></i>',
                'arrow-down'   => '<i class="fas fa-arrow-down"></i>',
                'arrows-alt'   => '<i class="fas fa-arrows-alt"></i>',
                'arrows-alt-h'   => '<i class="fas fa-arrows-alt-h"></i>',
                'arrows-alt-v'   => '<i class="fas fa-arrows-alt-v"></i>',

                'hand-point-right'  => '<i class="fas fa-hand-point-right"></i>',
                'hand-point-left'   => '<i class="fas fa-hand-point-left"></i>',
                'hand-point-up'     => '<i class="fas fa-hand-point-up"></i>',
                'hand-point-down'   => '<i class="fas fa-hand-point-down"></i>',

                'caret-square-right'  => '<i class="fas fa-caret-square-right"></i>',
                'caret-square-left'   => '<i class="fas fa-caret-square-left"></i>',
                'caret-square-up'     => '<i class="fas fa-caret-square-up"></i>',
                'caret-square-down'   => '<i class="fas fa-caret-square-down"></i>',
                
                'angle-double-right'  => '<i class="fas fa-angle-double-right"></i>',
                'angle-double-left'   => '<i class="fas fa-angle-double-left"></i>',
                'angle-double-up'     => '<i class="fas fa-angle-double-up"></i>',
                'angle-double-down'   => '<i class="fas fa-angle-double-down"></i>',
                
                'angle-right'  => '<i class="fas fa-angle-right"></i>',
                'angle-left'   => '<i class="fas fa-angle-left"></i>',
                'angle-up'     => '<i class="fas fa-angle-up"></i>',
                'angle-down'   => '<i class="fas fa-angle-down"></i>',
                
                'chevron-circle-right'  => '<i class="fas fa-chevron-circle-right"></i>',
                'chevron-circle-left'   => '<i class="fas fa-chevron-circle-left"></i>',
                'chevron-circle-up'     => '<i class="fas fa-chevron-circle-up"></i>',
                'chevron-circle-down'   => '<i class="fas fa-chevron-circle-down"></i>',
                
                'align-center'          => '<i class="fas fa-align-center"></i>',
                'align-left'            => '<i class="fas fa-align-left"></i>',
                'align-right'           => '<i class="fas fa-align-right"></i>',
                'align-justify'         => '<i class="fas fa-align-justify"></i>',
                
                'bell'                  => '<i class="fas fa-bell"></i>',
                'bell-slash'            => '<i class="fas fa-bell-slash"></i>',
                
                'user'              => '<i class="fas fa-user"></i>',
                'user-times'        => '<i class="fas fa-user-times"></i>',
                'user-minus'        => '<i class="fas fa-user-minus"></i>',
                'user-plus'         => '<i class="fas fa-user-plus"></i>',
                'user-alt'          => '<i class="fas fa-user-alt"></i>',
                'user-edit'          => '<i class="fas fa-user-edit"></i>',
                'user-tag'          => '<i class="fas fa-user-tag"></i>',
                'user-cog'          => '<i class="fas fa-user-cog"></i>',
                'user-check'          => '<i class="fas fa-user-check"></i>',
                'user-lock'          => '<i class="fas fa-user-lock"></i>',
                'user-slash'        => '<i class="fas fa-user-slash"></i>',
                'user-friends'        => '<i class="fas fa-user-friends"></i>',
                'user-shield'          => '<i class="fas fa-user-shield"></i>',
                'users'          => '<i class="fas fa-users"></i>',
                
                'tools'             => '<i class="fas fa-tools"></i>',
                'toolbox'           => '<i class="fas fa-toolbox"></i>',
                'hammer'            => '<i class="fas fa-hammer"></i>',
                'wrench'            => '<i class="fas fa-wrench"></i>',

                'file'           => '<i class="fas fa-file"></i>',
                'file-far'       => '<i class="far fa-file"></i>',
                'file-alt'            => '<i class="fas fa-file-alt"></i>',                
                'file-alt-far'        => '<i class="far fa-file-alt"></i>',             

                'file-csv'            => '<i class="fas fa-file-csv"></i>',                
                'file-image'            => '<i class="fas fa-file-image"></i>',                
                'file-image-far'            => '<i class="far fa-file-image"></i>',                
                'file-video'            => '<i class="fas fa-file-video"></i>',                
                'file-video-far'            => '<i class="far fa-file-video"></i>',                
                'file-audio'            => '<i class="fas fa-file-audio"></i>',                
                'file-audio-far'            => '<i class="far fa-file-audio"></i>',                
                
            ];    
    
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // デバッグディレクティブ
        //
        Blade::if( 'if_debug', function( $var = null ) {
            return is_debug(); 
        });
        
        Blade::directive( 'icon', function( $icon_name ) {

            if( array_key_exists( $icon_name, $this->icons )) {
                return $this->icons[$icon_name];
            } else {
                return $this->icons['times-circle'];
            }

        });      

        Blade::directive( 'all_icons', function( ) {
            
            $print = "Print All Icons. <BR>\n";
            foreach( $this->icons as $name => $icon ) {
                $print .= "<div class='icon_btn'>". $icon . " : " . $name . "</div>\n";
            }
            return $print;
            
            
        } );
        
        
    }
}
