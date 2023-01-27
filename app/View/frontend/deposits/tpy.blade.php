@extends('frontend._layouts.card')

@section('content')
    <table data-v-d19ad880="" class="sd-table">
        <tr data-v-d19ad880="">
            <td data-v-d19ad880="" style="background: rgb(232, 232, 232); width: 114px;">存款金额</td>
            <td data-v-d19ad880="" style="background: rgb(243, 243, 243); width: 374px;">
                <span data-v-d19ad880="" id="amount">{{$form['order_amount']}}</span>
                <div data-v-d19ad880="" data-clipboard-action="copy" data-clipboard-target="#amount"
                     class="copy2 amount" onclick="copyToClip('amount')">复制
                </div>
            </td>
        </tr>
        <tr data-v-d19ad880="">
            <td data-v-d19ad880="" style="background: rgb(240, 240, 242); width: 114px;">存款方式</td>
            <td data-v-d19ad880="" style="width: 374px;">离线存款</td>
        </tr>
        <tr data-v-d19ad880="">
            <td data-v-d19ad880="" style="background: rgb(232, 232, 232); width: 114px;">收款人姓名</td>
            <td data-v-d19ad880="" style="background: rgb(243, 243, 243); width: 374px;">
                <span data-v-d19ad880="" id="name">{{$form['bank_account_name']}}</span>
                <div data-v-d19ad880="" data-clipboard-action="copy" data-clipboard-target="#name"
                     class="copy2 name" onclick="copyToClip('name')">复制
                </div>
            </td>
        </tr>
        <tr data-v-d19ad880="">
            <td data-v-d19ad880="" style="background: rgb(240, 240, 242); width: 114px;">收款银行</td>
            <td data-v-d19ad880="" style="width: 374px;">
                <span data-v-d19ad880="" id="bank_name">{{$form['bank_ode']}} {{$form['bank_area']}}</span>
                <div data-v-d19ad880="" data-clipboard-action="copy" data-clipboard-target="#bank_name"
                     class="copy2 bank_name" onclick="copyToClip('bank_name')">复制
                </div>
            </td>
        </tr>
        <tr data-v-d19ad880="">
            <td data-v-d19ad880="" style="background: rgb(232, 232, 232); width: 114px;">银行卡号</td>
            <td data-v-d19ad880="" style="background: rgb(243, 243, 243); width: 374px;"><span
                        data-v-d19ad880="" id="card_number">{{$form['bank_account']}}</span>
                <div data-v-d19ad880="" data-clipboard-action="copy" data-clipboard-target="#card_number"
                     class="copy2 card_number" onclick="copyToClip('card_number')">复制
                </div>
            </td>
        </tr>
        <tr data-v-d19ad880="">
            <td data-v-d19ad880="" style="background: rgb(240, 240, 242); width: 114px;">附言</td>
            <td data-v-d19ad880="" style="width: 374px;">
                <span data-v-d19ad880="" id="note">{{$forms['remark']}}</span>
                <div data-v-d19ad880="" data-clipboard-action="copy" data-clipboard-target="#note"
                     class="copy2 note" onclick="copyToClip('note')">复制
                </div>
            </td>
        </tr>
    </table>
@endsection

