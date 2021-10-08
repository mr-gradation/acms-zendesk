# acms-zendesk
a-blog cms の拡張アプリ「Zendesk for a-blog cms」を使うと、フォームからデータが送信されたタイミングでZendeskにチケットを送信することができます。

# ダウンロード
[Zendesk for a-blog cms](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/Zendesk.zip)

# 使い方

### Zendeskの登録
1. Zendeskの「管理」→「API」より、「トークンアクセス」を有効にしてください。その際に「APIトークン」はコピーしておきます。

![zendesk_api-setting](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/zendesk-api-setting.png)

### a-blog cmsの登録

2. ダウンロード後、extension/plugins/Zendesk に設置してください。（フォルダ名は１文字目が大文字になります）

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/upload-plugin.png)

3. 管理ページ > 拡張アプリのページに移動し、Zendesk をインストールします。

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/register-plugin.png)

### a-blog cmsの拡張アプリ「Zendesk」を設定

4. 管理ページ > フォームの「変更」ページに移動すると、「Zendesk設定」が追加されていますので、「有効にする」にチェックを入れて、APIトークンを指定します。

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/plugin-setting-basic.png)

5. Zendeskのチケットに登録する件名と本文、送信者の名前とメールアドレスを登録します。一般メール設定、管理者宛メール設定と同様に、フォームのカスタムフィールドの変数を記載して出力する内容を登録します。IFブロックなどの条件分岐も登録することができます。

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/plugin-setting-fields.png)

6. フォームで送信を行うと、チケットが登録されます。

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/zendesk-receive-ticket.png)

### その他設定

カスタムフィールドがある場合は、ZendeskのカスタムフィールドのIDと、送信する値を指定することができます。

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/plugin-setting-customfields.png)

ブランドの指定を行うこともできます。ブランドの指定がない場合は、通常通りメール送信するといったオプションも用意しています。

![upload](https://raw.githubusercontent.com/mr-gradation/acms-zendesk/main/docs/plugin-setting-brand.png)



## 注意

config.server.phpでHOOKを有効にしておく必要があります。

```
define('HOOK_ENABLE', 1);
```
