import React from "react";
import { Form, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function ImageGalleryAppenderDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
        <Form.Item label="Include Duplicates">
            <Switch
                checked={s.includeDuplicates ?? false}
                onChange={(checked) => onChange({ ...settings, settings: { ...s, includeDuplicates: checked } })}
                size="small"
            />
        </Form.Item>
    );
}
