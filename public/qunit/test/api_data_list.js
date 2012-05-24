var templateList = new ApiDataList("/api/template");
var mediaList = new ApiDataList("/api/media");

test("getJSON JSONのデータを取得する", function() {

    var mediaTemplateData = [
        {
            comment:"2012-04-11 08:42:09にアップロード",
            create_date:"2012-04-11 08:42:09",
            id:"1",
            mediaExists:false,
            name:"image.jpeg",
            thumbExists:false,
            thumbUrl:"http://setucocms.localdomain/media/thumbnail/1.gif",
            thumbWidth:0,
            type:"jpg",
            update_date:"2012-05-22 18:11:40",
            uploadUrl:"http://setucocms.localdomain/media/upload/1.jpg"
        },

        {
            comment:"2012-04-11 08:42:09にアップロード",
            create_date:"2012-04-11 08:42:09",
            id:"2",
            mediaExists:false,
            name:"image.png",
            thumbExists:false,
            thumbUrl:"http://setucocms.localdomain/media/thumbnail/2.gif",
            thumbWidth:0,
            type:"png",
            update_date:"2012-04-11 08:42:09",
            uploadUrl:"http://setucocms.localdomain/media/upload/2.png"
        }
    ]

    deepEqual(mediaList.getJSONList(), mediaTemplateData);

    var expectedTemplateData = [
        {title:"TOPページのテンプレート", explanation:"TOPページのテンプレートです", url:"http://setucocms.localdomain/js/template/1.html"},
        {title:"一般ユーザーのテンプレート", explanation:"一般ユーザーが作成したテンプレートです", url:"http://setucocms.localdomain/js/template/2.html"}
    ]

    deepEqual(templateList.getJSONList(), expectedTemplateData);
    
});

test("getTinyMCEListData TinyMCE用のデータを取得する", function() {

    var mediaExpected = [
      ["image.jpeg", "http://setucocms.localdomain/media/upload/1.jpg"],
      ["image.png", "http://setucocms.localdomain/media/upload/2.png"]
    ];
    
    deepEqual(mediaList.getTinyMCEListDataByMapList(["name", "uploadUrl"]), mediaExpected);


    var templateExpected = [
        ["TOPページのテンプレート", "http://setucocms.localdomain/js/template/1.html", "TOPページのテンプレートです"],
        ["一般ユーザーのテンプレート", "http://setucocms.localdomain/js/template/2.html", "一般ユーザーが作成したテンプレートです"]
    ];

    deepEqual(templateList.getTinyMCEListDataByMapList(["title", "url", "explanation"]), templateExpected);

});