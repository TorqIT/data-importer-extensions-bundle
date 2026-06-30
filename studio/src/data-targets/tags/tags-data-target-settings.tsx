import React from "react";
import { Form, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function TagsDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
        <>
            <Form.Item label="Remove Other Tags">
                <Switch
                    checked={s.removeOtherTags ?? false}
                    onChange={(checked) => onChange({ ...settings, settings: { ...s, removeOtherTags: checked } })}
                    size="small"
                />
            </Form.Item>
            <Form.Item label="Create Tags if Not Exists">
                <Switch
                    checked={s.createTagsIfNotExists ?? false}
                    onChange={(checked) => onChange({ ...settings, settings: { ...s, createTagsIfNotExists: checked } })}
                    size="small"
                />
            </Form.Item>
        </>
    );
}
