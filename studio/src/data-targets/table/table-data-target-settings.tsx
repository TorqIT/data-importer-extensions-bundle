import React from "react";
import { Input } from "antd";
import { Form, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function TableDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
        <>
            <Form.Item label="Field Name">
                <Input
                    onChange={(e) => onChange({ ...settings, settings: { ...s, fieldName: e.target.value } })}
                    value={s.fieldName ?? ""}
                />
            </Form.Item>
            <Form.Item label="Write if Target is Not Empty">
                <Switch
                    checked={s.writeIfTargetIsNotEmpty ?? true}
                    onChange={(checked) => onChange({ ...settings, settings: { ...s, writeIfTargetIsNotEmpty: checked } })}
                    size="small"
                />
            </Form.Item>
            <Form.Item label="Write if Source is Empty">
                <Switch
                    checked={s.writeIfSourceIsEmpty ?? false}
                    onChange={(checked) => onChange({ ...settings, settings: { ...s, writeIfSourceIsEmpty: checked } })}
                    size="small"
                />
            </Form.Item>
        </>
    );
}
