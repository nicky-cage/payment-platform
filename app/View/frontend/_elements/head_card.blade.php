<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> 支付收银系统 </title>
<style>
    .second-direct[data-v-d19ad880] {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        width: 500px;
        margin: 10px auto;
    }

    .second-direct .transfer-center[data-v-d19ad880] {
        background: #f0f0f0;
        border-radius: 10px;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        height: 201px;
        margin-left: 68px;
        margin-top: 20px;
        width: 490px;
    }

    .second-direct .transfer-center .tc-title[data-v-d19ad880] {
        color: #4b4b4a;
        font-size: 14px;
        font-weight: 500;
        line-height: 33px;
        margin-left: 24px;
        margin-top: 4px;
    }

    .second-direct .transfer-center .tc-content[data-v-d19ad880] {
        margin-left: 24px;
    }

    .second-direct .transfer-center .tc-content .content-text[data-v-d19ad880] {
        color: #4b4b4a;
        font-size: 12px;
        font-weight: 500;
        line-height: 23px;
    }

    .second-direct .sd-table[data-v-d19ad880] {
        border: 1px solid #a5aabc;
        color: #4b4b4a;
        font-size: 12px;
        font-weight: 500;
        height: 192px;
        line-height: 32px;
        margin-left: 68px;
        margin-top: 20px;
        text-align: center;
        width: 488px;
    }

    .second-direct .sd-table tr[data-v-d19ad880] {
        height: 32px;
    }

    .second-direct .sd-table tr td[data-v-d19ad880] {
        position: relative;
    }

    .second-direct .sd-table .copy2[data-v-d19ad880] {
        background: #fff;
        border-radius: 9px;
        border: 1px solid #e2e2e2;
        color: #474747;
        font-size: 12px;
        font-weight: 500;
        height: 20px;
        line-height: 18px;
        margin-left: 32px;
        position: absolute;
        right: 15px;
        top: 6px;
        width: 48px;
        cursor: pointer;
    }

    table {
        text-align: center; /*文本居中*/
        border-collapse: collapse; /*表格的边框合并，如果相邻，则共用一个边框*/
        border-spacing: 0; /*设置行与单元格边框的间距。当表格边框独立（即border-collapse:separate;）此属性才起作用*/
    }
</style>
<script>
    function copyToClip(dom) {
        let content = document.getElementById(dom).innerText
        let aux = document.createElement("input");
        aux.setAttribute("value", content);
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
    }
</script>