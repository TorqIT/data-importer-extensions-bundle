import { DynamicTypeAbstract, DynamicTypeRegistryAbstract } from "@pimcore/studio-ui-bundle/modules/element";
import React from "react";

export interface DynamicTypeResolverRenderProps {
    columnHeaderOptions: Array<{ value: string; label: string }>;
    languageOptions?: Array<{ value: string; label: string }>;
}

export type ResolverGroup = "loading" | "createLocation" | "updateLocation" | "publishing";

export interface DynamicTypeResolver extends DynamicTypeAbstract {
    readonly type: string;
    readonly label: string;
    readonly group: ResolverGroup;
    renderSettings(props: DynamicTypeResolverRenderProps): React.JSX.Element | null;
}

export interface DynamicTypeResolverRegistry extends DynamicTypeRegistryAbstract<DynamicTypeResolver> {
    getDynamicTypesForGroup(group: ResolverGroup): DynamicTypeResolver[];
}
