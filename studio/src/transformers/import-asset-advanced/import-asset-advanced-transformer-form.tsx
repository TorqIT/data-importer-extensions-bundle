import React from "react";
import { Form, Input } from "@pimcore/studio-ui-bundle/components";
import { TransformerSettingsLayout } from "../../common/components/transformer-settings-layout";

// Extends the core importAsset transformer with path and urlPropertyName settings.
// TODO: The path field ideally uses a Pimcore asset tree picker — for now uses a text input.

interface ImportAssetAdvancedTransformerConfig {
    path?: string;
    urlPropertyName?: string;
}

interface ImportAssetAdvancedTransformerFormProps {
    settings: ImportAssetAdvancedTransformerConfig;
    onChange: (settings: ImportAssetAdvancedTransformerConfig) => void;
}

export const ImportAssetAdvancedTransformerForm = ({
    settings,
    onChange,
}: ImportAssetAdvancedTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: string): void => {
        onChange({ ...settings, [key]: value });
    };

    return (
        <TransformerSettingsLayout>
            <Form.Item label="Asset Path">
                <Input
                    onChange={(e) => {
                        update("path", e.target.value);
                    }}
                    placeholder="/"
                    value={settings.path ?? "/"}
                />
            </Form.Item>
            <Form.Item label="URL Property Name">
                <Input
                    onChange={(e) => {
                        update("urlPropertyName", e.target.value);
                    }}
                    value={settings.urlPropertyName ?? ""}
                />
            </Form.Item>
        </TransformerSettingsLayout>
    );
};
