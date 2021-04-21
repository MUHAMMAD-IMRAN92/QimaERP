<?php

namespace App\Http\Controllers;

use App\Farmer;
use Illuminate\Http\Request;

class DevTestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv3';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route V3');

        $rawFarmers = collect(array(
            array('علي ناجي صالح الرميم', 'SAN -HAY -03 -001'),
            array('عماد صالح صالح الرميم', 'SAN -HAY -03 -002'),
            array('حمير محمد علي صبيح', 'SAN -HAY -03 -003'),
            array('محمد يحيى محمد كليب', 'SAN -HAY -03 -004'),
            array('حميد محمد عبده صبيح', 'SAN -HAY -03 -005'),
            array('مجيب صالح صالح الرميم', 'SAN -HAY -03 -006'),
            array('احمد صالح صالح الرميم', 'SAN -HAY -03 -007'),
            array('حاشد راجح عبده صبيح', 'SAN -HAY -03 -008'),
            array('يحيى يحيى حزام اللهبه', 'SAN -HAY -03 -009'),
            array('محمد يحيى محمد كليب', 'SAN -HAY -03 -010'),
            array('حسين احمد محسن اللهبه', 'SAN -HAY -03 -011'),
            array('زياد عبده الرميم', 'SAN -HAY -03 -012'),
            array('عبدالسلام احمد محسن اللهبه', 'SAN -HAY -03 -013'),
            array('محمد مبارك صالح كليب', 'SAN -HAY -03 -014'),
            array('ناصر ناصر حسين اللهبه', 'SAN -HAY -03 -015'),
            array('عبده احمد صالح صبيح', 'SAN -HAY -03 -016'),
            array('عبده صبيح علي صبيح القدح', 'SAN -HAY -03 -017'),
            array('علي حمود علي كليب', 'SAN -HAY -03 -018'),
            array('عبده علي الرميم', 'SAN -HAY -03 -019'),
            array('عبده صالح حزام اللهبه', 'SAN -HAY -03 -020'),
            array('احمد حمود محمد كليب', 'SAN -HAY -03 -021'),
            array('احمد علي حسين اللهبه', 'SAN -HAY -03 -022'),
            array('احمد صالح حزام اللهبه', 'SAN -HAY -03 -023'),
            array('يحيى محمد علي صبيح', 'SAN -HAY -03 -024'),
            array('محمد نايف كليب', 'SAN -HAY -03 -025'),
            array('يوسف عبدالله الرميم', 'SAN -HAY -03 -026'),
            array('عبده صالح علي صبيح', 'SAN -HAY -03 -027'),
            array('محمد محمد علي كليب', 'SAN -HAY -03 -028'),
            array('عبدالله عبده صبيح', 'SAN -HAY -03 -029'),
            array('احمد محمد حسين الزبر', 'SAN -HAY -03 -030'),
            array('احمد احمد صالح صبيح', 'SAN -HAY -03 -031'),
            array('احمد محمد صالح كليب', 'SAN -HAY -03 -032'),
            array('محمد احمد محسن اللهبه', 'SAN -HAY -03 -033'),
            array('يحيى هادي الطويل', 'SAN -HAY -03 -034'),
            array('ياسين احمد محمد كليب', 'SAN -HAY -03 -035'),
            array('علي احمد محسن اللهبه', 'SAN -HAY -03 -036'),
            array('علي صالح صالح صبيح', 'SAN -HAY -03 -037'),
            array('عبده دعر يحيى دعر', 'SAN -HAY -03 -038'),
            array('يحيى احمد صالح صالح صبيح', 'SAN -HAY -03 -039'),
            array('ماجد صالح الرميم', 'SAN -HAY -03 -040'),
            array('امين احمد حمود صبيح', 'SAN -HAY -03 -041'),
            array('يحيى علي صالح صبيح', 'SAN -HAY -03 -042'),
            array('حسين يحيى حسين اللهبه', 'SAN -HAY -03 -043'),
            array('علي احمد صالح صبيح', 'SAN -HAY -03 -044'),
            array('احمد ناصر ناصر اللهبه', 'SAN -HAY -03 -045'),
            array('الشاذلي محمد صبيح', 'SAN -HAY -03 -046'),
            array('محمد علي صبيح القحم', 'SAN -HAY -03 -047'),
            array('محمد احمد علي صبيح', 'SAN -HAY -03 -048'),
            array('احمد محمد اللهيم', 'SAN -HAY -03 -049'),
            array('علي محمد حمود كليب', 'SAN -HAY -03 -050'),
            array('احمد عبده علي الرميم', 'SAN -HAY -03 -051'),
            array('عدنان ناجي محمود صبيح', 'SAN -HAY -03 -052'),
            array('مبارك محمد علي صبيح', 'SAN -HAY -03 -053'),
            array('محمد علي صالح صبيح', 'SAN -HAY -03 -054'),
            array('معين سعد صبيح', 'SAN -HAY -03 -055'),
            array('جابر صبيح علي صبيح', 'SAN -HAY -03 -056'),
            array('احمد محمد عبدالله القحم', 'SAN -HAY -03 -057'),
            array('قايد عامر صالح سعد صبيح', 'SAN -HAY -03 -058'),
            array('محمد عبده قاسم', 'SAN -HAY -03 -059'),
            array('علي محمد عامر سعد صبيح', 'SAN -HAY -03 -060'),
            array('عادل محمد صبيح', 'SAN -HAY -03 -061'),
            array('ماجد عبده صالح صبيح', 'SAN -HAY -03 -062'),
            array('عبدالله حسين يحيى كليب', 'SAN -HAY -03 -063'),
            array('احمد عبدالله علي الرميم', 'SAN -HAY -03 -064'),
            array('عرشي محمد صالح صبيح', 'SAN -HAY -03 -065'),
            array('صدام حسين كليب', 'SAN -HAY -03 -066'),
            array('علي محمد صبيح', 'SAN -HAY -03 -067'),
            array('صالح صالح علي صبيح', 'SAN -HAY -03 -068'),
            array('يحيى احمد عامر صبيح', 'SAN -HAY -03 -069'),
            array('علي محمد محمد صبيح', 'SAN -HAY -03 -070'),
            array('عبدالله عبدالله محمد صبيح', 'SAN -HAY -03 -071'),
            array('محمد علي حمود كليب', 'SAN -HAY -03 -072'),
            array('صالح عامر سعد صبيح', 'SAN -HAY -03 -073'),
            array('اكرم قايد قايد صبيح', 'SAN -HAY -03 -074'),
            array('سلطان سعد صبيح علي', 'SAN -HAY -03 -075'),
            array('عدنان علي احمد صبيح', 'SAN -HAY -03 -076'),
            array('فؤاد احمد احمد صبيح', 'SAN -HAY -03 -077'),
            array('مبارك حزام مبارك صبيح', 'SAN -HAY -03 -078'),
            array('عبده عبده علي الرميم', 'SAN -HAY -03 -079'),
            array('احمد حمود صالح كليب', 'SAN -HAY -03 -080'),
            array('محمد عبدالله محمد صبيح', 'SAN -HAY -03 -081'),
            array('صالح حميد صبيح', 'SAN -HAY -03 -082'),
            array('نشوان علي نشوان', 'SAN -HAY -03 -083'),
            array('معين احسن محمد صبيح', 'SAN -HAY -03 -084'),
            array('طه محمد محمد الرميم', 'SAN -HAY -03 -085'),
            array('عايش احمد صبيح', 'SAN -HAY -03 -086'),
            array('اكرم حميد صبيح', 'SAN -HAY -03 -087'),
            array('احمد حمود علي كليب', 'SAN -HAY -03 -088'),
            array('علي احمد محمد صبيح', 'SAN -HAY -03 -089'),
            array('يحيى يحيى علي نشوان', 'SAN -HAY -03 -090'),
            array('احمد احمد علي صبيح', 'SAN -HAY -03 -091'),
            array('حميد محمد صبيح علي صبيح', 'SAN -HAY -03 -092'),
            array('مبروك محمد علي صبيح', 'SAN -HAY -03 -093'),
            array('حفظ الله محمد صبيح', 'SAN -HAY -03 -094'),
            array('محمد احمد احمد الرميم', 'SAN -HAY -03 -095'),
            array('مطهر احمد صالح نشوان', 'SAN -HAY -03 -096'),
            array('محمد محمد علي هادي الرميم', 'SAN -HAY -03 -097'),
            array('وليد ناصر ناصر اللهبه', 'SAN -HAY -03 -098'),
            array('احمد يحيى يحيى اللهبه', 'SAN -HAY -03 -099'),
            array('مبارك محمد يحيى الرميم', 'SAN -HAY -03 -100'),
            array('ابراهيم حسين محسن الرميم', 'SAN -HAY -03 -101'),
            array('يوسف محمد علي الرميم', 'SAN -HAY -03 -102'),
            array('علي عامر صالح صبيح', 'SAN -HAY -03 -103'),
            array('ياسين محمد يحيى الرميم', 'SAN -HAY -03 -104'),
            array('محمد احمد علي هادي الرميم', 'SAN -HAY -03 -105'),
            array('احمد صالح احمد نشوان', 'SAN -HAY -03 -106'),
            array('محمد علي ناجي القحم', 'SAN -HAY -03 -108'),
            array('عبدالله عبدالله علي صبيح', 'SAN -HAY -03 -109'),
            array('حفظ الله علي عبده الحيمي', 'SAN -HAY -03 -110'),
            array('محمد احمد عبدالله القحم', 'SAN -HAY -03 -111'),
            array('محمد عبده علي الرميم', 'SAN -HAY -03 -112'),
            array('علي محمد عبده مهدي', 'SAN -HAY -03 -113'),
            array('حمادي محمد علي صبيح', 'SAN -HAY -03 -114'),
            array('خالد محمد مبارك كليب', 'SAN -HAY -03 -115'),
            array('جابر القحم', 'SAN -HAY -03 -116'),
            array('يحيى صالح احسن داعر', 'SAN -HAY -03 -117'),
            array('محمد علي حاج القحم', 'SAN -HAY -03 -118'),
            array('محمد محمد حسين الزبر', 'SAN -HAY -03 -119'),
            // array('مجمع الحيمة الرواد', 'SAN -HAY -30 -001'),
        ));

        $farmers = collect();

        $rawFarmers->each(function ($rawFarmer) use ($farmers) {
            $farmer = new Farmer();

            $farmer->farmer_code = str_replace(' ', '', $rawFarmer[1]);
            $farmer->farmer_name = $rawFarmer[0];
            $farmer->village_code = 'SAN-HAY-03';
            $farmer->is_status = 1;
            $farmer->created_by = 4;
            $farmer->local_code = str_replace(' ', '', $rawFarmer[1]);
            $farmer->farmer_nicn = '0000000';

            $farmer->save();

            $farmers->push($farmer);
        });

        return $farmers;
    }
}
