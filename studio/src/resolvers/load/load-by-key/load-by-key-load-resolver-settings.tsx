import React from "react";
import { Input } from "antd";
import { Form, FormKit, Select, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function LoadByKeyLoadResolverSettings({ columnHeaderOptions }: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item
                name={["resolverConfig", "loadingStrategy", "settings", "dataSourceIndex"]}
                label="Data Source Index"
            >
                <Select options={columnHeaderOptions} showSearch />
            </Form.Item>
            <Form.Item
                name={["resolverConfig", "loadingStrategy", "settings", "searchPath"]}
                label="Search Path"
                extra="Restrict search to a specific folder path"
            >
                <Input placeholder="(no restriction)" />
            </Form.Item>
            <Form.Item
                name={["resolverConfig", "loadingStrategy", "settings", "includeUnpublished"]}
                valuePropName="checked"
            >
                <Switch labelRight="Include Unpublished" size="small" />
            </Form.Item>
        </FormKit.Panel>
    );
}
