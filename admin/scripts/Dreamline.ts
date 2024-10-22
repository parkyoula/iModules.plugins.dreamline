/**
 * 이 파일은 아이모듈 드림라인 SMS 플러그인의 일부입니다. (https://www.imodules.io)
 *
 * 관리자 UI 이벤트를 관리하는 클래스를 정의한다.
 *
 * @file /plugins/dreamline/admin/scripts/Dreamline.ts
 * @author Arzz <arzz@arzz.com>
 * @license MIT License
 * @modified 2024. 10. 22.
 */
namespace plugins {
    export namespace dreamline {
        export namespace admin {
            export class Dreamline extends modules.admin.admin.Component {
                /**
                 * 플러그인 환경설정 폼을 가져온다.
                 *
                 * @return {Promise<Aui.Form.Panel>} configs
                 */
                async getConfigsForm(): Promise<Aui.Form.Panel> {
                    return new Aui.Form.Panel({
                        items: [
                            new Aui.Form.FieldSet({
                                title: await this.getText('admin.configs.api'),
                                items: [
                                    new Aui.Form.Field.RadioGroup({
                                        label: this.printText('admin.configs.id_type'),
                                        name: 'id_type',
                                        columns: 2,
                                        options: {
                                            MID: 'MID',
                                            SID: 'SID',
                                        },
                                        helpText: this.printText('admin.configs.id_type_help'),
                                    }),
                                    new Aui.Form.Field.Text({
                                        label: this.printText('admin.configs.id'),
                                        name: 'id',
                                        helpText: this.printText('admin.configs.id_help'),
                                    }),
                                    new Aui.Form.Field.Text({
                                        label: this.printText('admin.configs.auth_key'),
                                        name: 'auth_key',
                                        helpText: this.printText('admin.configs.auth_key_help'),
                                    }),
                                    new Aui.Form.Field.Text({
                                        label: this.printText('admin.configs.cellphone'),
                                        name: 'cellphone',
                                        helpText: this.printText('admin.configs.cellphone_help'),
                                    }),
                                ],
                            }),
                        ],
                    });
                }
            }
        }
    }
}
