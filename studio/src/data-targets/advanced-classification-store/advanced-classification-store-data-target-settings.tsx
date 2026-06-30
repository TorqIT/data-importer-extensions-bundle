import React from "react";
import { Form, Select, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function AdvancedClassificationStoreDataTargetSettings({
    classFieldOptions,
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};
    const writeIfTargetIsNotEmpty = s.writeIfTargetIsNotEmpty ?? true;

    return (
        <>
            <Form.Item label="Field Name">
                <Select
                    onChange={(v) => onChange({ ...settings, settings: { ...s, fieldName: v } })}
                    options={classFieldOptions}
                    value={s.fieldName}
                />
            </Form.Item>
            <Form.Item label="Write if Target is Not Empty">
                <Switch
                    checked={writeIfTargetIsNotEmpty}
                    onChange={(checked) =>
                        onChange({
                            ...settings,
                            settings: {
                                ...s,
                                writeIfTargetIsNotEmpty: checked,
                                writeIfSourceIsEmpty: checked ? s.writeIfSourceIsEmpty : false,
                            },
                        })
                    }
                    size="small"
                />
            </Form.Item>
            <Form.Item label="Write if Source is Empty">
                <Switch
                    checked={s.writeIfSourceIsEmpty ?? false}
                    disabled={!writeIfTargetIsNotEmpty}
                    onChange={(checked) => onChange({ ...settings, settings: { ...s, writeIfSourceIsEmpty: checked } })}
                    size="small"
                />
            </Form.Item>
        </>
    );
}
