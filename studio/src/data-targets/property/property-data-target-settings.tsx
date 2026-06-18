import React from "react";
import { Input } from "antd";
import { Form } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function PropertyDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
        <Form.Item label="Property Name" required>
            <Input
                onChange={(e) => onChange({ ...settings, settings: { ...s, propertyName: e.target.value } })}
                value={s.propertyName ?? ""}
            />
        </Form.Item>
    );
}
