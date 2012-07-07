/**
 * APIモジュールからデータを取得します
 *
 * 使用するにはjQueryを使用できるようにしていないといけない
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category   Setuco
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

var ApiDataList = function(apiUrl){

    this.apiUrl = apiUrl;

    this.getJSONList = function() {

        var jsonDatas;

        $.ajax({
            type:   "GET",
            url:    this.apiUrl,
            async:  false,

            success: function(json) {
                jsonDatas = json;
            }
        });

        return jsonDatas;
    }

    /**
     * TinyMCEとの関連性を持たせすぎないために引数でmapListを渡すようにする
     */
    this.getTinyMCEListDataByMapList = function(mapList) {
       var listDatas = new Array();

       $.each(this.getJSONList(), function(){

          var data = new Array();
          var json = this;

          for (i in mapList) {
              data.push(json[mapList[i]]);
          }

          listDatas.push(data);
       });

       return listDatas;
    }
}