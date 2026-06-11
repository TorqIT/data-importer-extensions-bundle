import React from "react";
import { Form, Select } from "@pimcore/studio-ui-bundle/components";
import { TransformerSettingsLayout } from "../../common/components/transformer-settings-layout";

const VIDEO_TYPES = [
    { value: "youtube", label: "YouTube" },
    { value: "vimeo", label: "Vimeo" },
    { value: "dailymotion", label: "Dailymotion" },
    { value: "url", label: "URL" },
];

interface AsVideoTransformerConfig {
    videoType?: string;
}

interface AsVideoTransformerFormProps {
    settings: AsVideoTransformerConfig;
    onChange: (settings: AsVideoTransformerConfig) => void;
}

export const AsVideoTransformerForm = ({ settings, onChange }: AsVideoTransformerFormProps): React.JSX.Element => (
    <TransformerSettingsLayout>
        <Form.Item label="Video Type">
            <Select
                onChange={(v) => {
                    onChange({ ...settings, videoType: v });
                }}
                options={VIDEO_TYPES}
                value={settings.videoType ?? "youtube"}
            />
        </Form.Item>
    </TransformerSettingsLayout>
);
