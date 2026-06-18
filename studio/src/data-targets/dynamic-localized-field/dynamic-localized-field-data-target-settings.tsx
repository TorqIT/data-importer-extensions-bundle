import React from "react";
import { Input } from "antd";
import { Form } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function DynamicLocalizedFieldDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
        <Form.Item label="Field Name">
            <Input
                onChange={(e) => onChange({ ...settings, settings: { ...s, fieldName: e.target.value } })}
                value={s.fieldName ?? ""}
            />
        </Form.Item>
    );
}
