<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PwaController extends Controller
{
    use Upload;

    public function create(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('admin.control_panel.pwa');
        } elseif ($request->isMethod('POST')) {
            $request->validate([
                'name' => 'string|max:255',
                'short_name' => 'string|max:50',
                'background_color' => 'regex:/^#[0-9a-fA-F]{6}$/',
                'theme_color' => 'regex:/^#[0-9a-fA-F]{6}$/',
                'display' => 'in:standalone,fullscreen,minimal-ui',
                'status_bar' => 'in:black,white',
            ]);


            $data =  $request->mergeIfMissing([
                'name' => 'Ex Rate',
                'short_name' => 'Ex Rate',
                'background_color' => '#ffffff',
                'theme_color' => '#000000',
                'display' => 'standalone',
                'status_bar' => 'black',
            ]);

            config(['laravelpwa.manifest.name' => $data['name']]);
            config(['laravelpwa.manifest.short_name' => $data['short_name']]);
            config(['laravelpwa.manifest.background_color' => $data['background_color']]);
            config(['laravelpwa.manifest.theme_color' => $data['theme_color']]);
            config(['laravelpwa.manifest.display' => $data['display']]);
            config(['laravelpwa.manifest.status_bar' => $data['status_bar']]);
            $fp = fopen(base_path() . '/config/laravelpwa.php', 'w');
            fwrite($fp, '<?php return ' . var_export(config('laravelpwa'), true) . ';');
            fclose($fp);



            if ($request->hasFile('icon')) {
                $file = $request->icon;
                foreach (config('pwacontrol.icons') as $key => $icon) {
                    $upload = $this->fileUpload($file, config('filelocation.pwa.path'), 'icon-' . $key, $key, 'png', 90, $icon, config('pwacontrol.driver'));
                    $this->saveConfig($upload, $key, 'icons');
                }

            }
        }

        if ($request->hasFile('splash')) {
            $file = $request->splash;
            foreach (config('pwacontrol.splash') as $key => $splash) {
                $upload = $this->fileUpload($file, config('filelocation.pwa.path'), 'splash-' . $key, $key, 'png', 90, $icon, config('pwacontrol.driver'));
                $this->saveConfig($upload, $key, 'splash');
            }
        }
        return back()->with('success', 'Successfully update pwa configuration');
    }

    public function saveConfig($upload, $key, $type): void
    {
        config(['pwacontrol.driver' => $upload['driver']]);
        config(['pwacontrol.' . $type . '.' . $key => $upload['path']]);
        $fp = fopen(base_path() . '/config/pwacontrol.php', 'w');
        fwrite($fp, '<?php return ' . var_export(config('pwacontrol'), true) . ';');
        fclose($fp);

        Artisan::call('optimize:clear');

        if ($type == 'icons') {
            config(['laravelpwa.manifest.' . $type . '.' . $key . '.path' => getFile($upload['driver'], $upload['path'])]);
        } else {
            config(['laravelpwa.manifest.' . $type . '.' . $key => getFile($upload['driver'], $upload['path'])]);
        }
        $fp = fopen(base_path() . '/config/laravelpwa.php', 'w');
        fwrite($fp, '<?php return ' . var_export(config('laravelpwa'), true) . ';');
        fclose($fp);

    }
}
