import React from "react";
import { Checkbox, Form, Input } from "@pimcore/studio-ui-bundle/components";
import { TransformerSettingsLayout } from "../../common/components/transformer-settings-layout";

// Splits a delimited taxonomy string into a branch under Path Prefix, creating any
// missing level, and returns the leaf object for mapping onto a relation field.
// TODO: pathPrefix should use a Pimcore object folder tree picker, objectClass a class picker.

interface LoadOrCreateByPathTransformerConfig {
    pathPrefix?: string;
    separator?: string;
    objectClass?: string;
    nameField?: string;
    createIfNotFound?: boolean;
    publishOnCreate?: boolean;
    exactMatch?: boolean;
    logNearMisses?: boolean;
    returnAllLevels?: boolean;
}

interface LoadOrCreateByPathTransformerFormProps {
    settings: LoadOrCreateByPathTransformerConfig;
    onChange: (settings: LoadOrCreateByPathTransformerConfig) => void;
}

export const LoadOrCreateByPathTransformerForm = ({
    settings,
    onChange,
}: LoadOrCreateByPathTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: any): void => {
        onChange({ ...settings, [key]: value });
    };

    return (
      <TransformerSettingsLayout>
        <Form.Item label="Path Prefix">
          <Input
            onChange={ (e) => {
                        update("pathPrefix", e.target.value);
                    } }
            placeholder="/ECCO Manufacturing/Categories/Website"
            value={ settings.pathPrefix ?? "" }
          />
        </Form.Item>
        <Form.Item label="Separator">
          <Input
            onChange={ (e) => {
                        update("separator", e.target.value);
                    } }
            placeholder=" > "
            value={ settings.separator ?? " > " }
          />
        </Form.Item>
        <Form.Item label="Object Class">
          <Input
            onChange={ (e) => {
                        update("objectClass", e.target.value);
                    } }
            placeholder="Category"
            value={ settings.objectClass ?? "" }
          />
        </Form.Item>
        <Form.Item label="Name Field">
          <Input
            onChange={ (e) => {
                        update("nameField", e.target.value);
                    } }
            placeholder="name"
            value={ settings.nameField ?? "" }
          />
        </Form.Item>
        <Form.Item label="Create if Not Found">
          <Checkbox
            checked={ settings.createIfNotFound ?? true }
            onChange={ (e) => {
                        update("createIfNotFound", e.target.checked);
                    } }
          />
        </Form.Item>
        <Form.Item label="Publish on Create">
          <Checkbox
            checked={ settings.publishOnCreate ?? true }
            onChange={ (e) => {
                        update("publishOnCreate", e.target.checked);
                    } }
          />
        </Form.Item>
        <Form.Item label="Exact Key Match Only">
          <Checkbox
            checked={ settings.exactMatch ?? false }
            onChange={ (e) => {
                        update("exactMatch", e.target.checked);
                    } }
          />
        </Form.Item>
        <Form.Item label="Log Normalised Matches">
          <Checkbox
            checked={ settings.logNearMisses ?? true }
            onChange={ (e) => {
                        update("logNearMisses", e.target.checked);
                    } }
          />
        </Form.Item>
        <Form.Item label="Return All Levels">
          <Checkbox
            checked={ settings.returnAllLevels ?? false }
            onChange={ (e) => {
                        update("returnAllLevels", e.target.checked);
                    } }
          />
        </Form.Item>
      </TransformerSettingsLayout>
    );
};
